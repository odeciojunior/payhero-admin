<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Events\CheckTransactionReleasedEvent;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;

class CheckWithdrawalsReleased extends Command
{
    protected $signature = 'getnet:check-withdrawals-released';

    protected $description = 'Command para transferir as transactions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {
            $withdrawals = Withdrawal::with('transactions', 'transactions.sale', 'transactions.company')
                ->where('gateway_id', Gateway::GETNET_PRODUCTION_ID)
                ->where('automatic_liquidation', true)
                ->where('is_released', false)
                ->whereIn('status', [Withdrawal::STATUS_LIQUIDATING, Withdrawal::STATUS_PARTIALLY_LIQUIDATED])
                ->orderBy('id');

            $withdrawals->chunk(
                500,
                function ($withdrawals) {
                    foreach ($withdrawals as $withdrawal) {
                        $getnetService = new GetnetBackOfficeService();

                        $withdrawalTransactionsCount = $withdrawal->transactions
                            ->where('gateway_id', Gateway::GETNET_PRODUCTION_ID)
                            ->whereNull('gateway_transferred_at')
                            ->count();

                        $countTransactionsReleased = 0;

                        $transactions = $withdrawal->transactions()->whereNull('gateway_transferred_at')->get();
                        foreach ($transactions as $transaction) {
                            if ($transaction->gateway_id != Gateway::GETNET_PRODUCTION_ID) {
                                continue;
                            }

                            $sale = $transaction->sale;
                            $orderId = $sale->gateway_order_id;

                            $response = $getnetService
                                ->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                                ->setStatementSubSellerId(CompanyService::getSubsellerId($transaction->company))
                                ->getStatement();

                            $gatewaySale = json_decode($response);

                            if (
                                isset($gatewaySale->list_transactions) &&
                                isset($gatewaySale->list_transactions[0]) &&
                                isset($gatewaySale->list_transactions[0]->details) &&
                                isset($gatewaySale->list_transactions[0]->details[0]) &&
                                isset($gatewaySale->list_transactions[0]->details[0]->release_status)
                            ) {
                                if ($gatewaySale->list_transactions[0]->details[0]->release_status == 'S') {
                                    $countTransactionsReleased++;
                                } elseif ($gatewaySale->list_transactions[0]->details[0]->release_status == 'N') {
                                    event(new CheckTransactionReleasedEvent($transaction->id));
                                }
                            } else {
                                if (isset($gatewaySale->list_transactions)) {
                                    $errorGetnet = 'Erro na estrutura da venda da Getnet. $sale->id = ' . $sale->id . ' $orderId = ' . $orderId;

                                    if (count($gatewaySale->list_transactions) == 0) {
                                        $response = $getnetService
                                            ->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                                            ->getStatement();

                                        $gatewaySale = json_decode($response);

                                        if (
                                            isset($gatewaySale->list_transactions)
                                            && isset($gatewaySale->list_transactions[0])
                                            && isset($gatewaySale->list_transactions[0]->summary)
                                            && isset($gatewaySale->list_transactions[0]->summary->reason_message)
                                            && $gatewaySale->list_transactions[0]->summary->reason_message == 'CANCELADA NAO CONFIRMADA'
                                        ) {
                                            $errorGetnet = 'Venda na Getnet está como "CANCELADA NAO CONFIRMADA". $sale->id = ' . $sale->id . ' $orderId = ' . $orderId;
                                        }
                                    }
                                    $this->warn($errorGetnet);
                                    //report(new Exception($errorGetnet));
                                }

                                $this->tryFixGatewayOrderIdAndGatewayTransactionId($sale);
                            }

                            if ($countTransactionsReleased == $withdrawalTransactionsCount) {
                                $withdrawal->update(['is_released' => true]);
                            }
                        }
                    }
                }
            );
        } catch (Exception $e) {
            report($e);
        }

    }

    private function tryFixGatewayOrderIdAndGatewayTransactionId(Sale $sale)
    {
        $saleId = $sale->id;

        $gatewayPostBacks = DB::table('gateway_postbacks')
            ->select('data')
            ->where('sale_id', $saleId)
            ->orderByDesc('id')
            ->get();

        $paidId = null;

        foreach ($gatewayPostBacks as $gatewayPostBackItem) {
            $gatewayPostBack = json_decode($gatewayPostBackItem->data);

            if (isset($gatewayPostBack->status) && $gatewayPostBack->status == "PAID") {
                $paidId = $gatewayPostBack->id;
            } elseif (isset($gatewayPostBack->id) && $gatewayPostBack->id == $paidId) {
                $orderId = $gatewayPostBack->order_id;
                $paymentId = $gatewayPostBack->payment_id;

                if ($orderId != $sale->gateway_order_id) {
                    Sale::find($saleId)->update(['gateway_order_id' => $orderId]);
                }

                if ($paymentId != $sale->gateway_transaction_id) {
                    Sale::find($saleId)->update(['gateway_transaction_id' => $paymentId]);
                }
            }
        }
    }
}
