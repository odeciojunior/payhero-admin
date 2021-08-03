<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\TransactionCloudfox;
use Modules\Core\Events\CheckTransactionReleasedEvent;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;
use Vinkla\Hashids\Facades\Hashids;

class CheckLiquidationTransactionsCloudfox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:liquidation-transaction-cloudfox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $getnetService = new GetnetBackOfficeService();
        $data = Carbon::createFromFormat('d/m/Y', '30/07/2021');
        $company = Company::find(2);

        $aux = 0;
        while ($data->lessThan(Carbon::now())) {
            if ($aux == 4) {
                $getnetService = new GetnetBackOfficeService();
                $aux = 0;
            }
            $aux++;

            $startDate = $data;
            $endDate = Carbon::parse($startDate)->addDays(30);
            $statementDateField = 'schedule';

            $response = $getnetService
                ->setStatementSubSellerId(CompanyService::getSubsellerId($company))
                ->setStatementStartDate($startDate)
                ->setStatementEndDate($endDate)
                ->setStatementDateField($statementDateField)
                ->getStatement();

            $gatewaySale = json_decode($response);

            foreach ($gatewaySale->list_transactions as $list_transaction) {
                if (
                    isset($list_transaction) &&
                    isset($list_transaction->details) &&
                    isset($list_transaction->details[0]) &&
                    isset($list_transaction->details[0]->release_status)

                ) {
                    foreach ($list_transaction->details as $detail) {
                        if ($detail->release_status == 'N' and $detail->transaction_sign = '+') {
                            $orderId = $list_transaction->summary->order_id;
                            $sale = Sale::where('gateway_order_id', $orderId)->first();
                            $transaction = Transaction::where('user_id', $sale->owner_id)->where('sale_id', $sale->id)->first();

                            $transactionCloudfox = TransactionCloudfox::create(
                                [
                                    'sale_id' => $sale->id,
                                    'gateway_id' => $transaction->gateway_id,
                                    'company_id' => $company->id,
                                    'user_id' => $transaction->user_id,
                                    'value' => $detail->subseller_rate_amount,
                                    'status' => 'paid',
                                    'status_enum' => 2,
                                    'release_date' => now()->format('Y-m-d')
                                ]
                            );

                            $this->line('Sale id: ' .  $sale->id . ', Transaction id: ' . $transaction->id . ', Transaction Cloudfox id: ' . $transactionCloudfox->id );

                            $data = [
                                'gateway_transaction_id' => $sale->gateway_transaction_id,
                                'plan_id' => Hashids::encode($sale->plansSales->first()->id),
                                'value' => $detail->subseller_rate_amount,
                                'transaction_cloudfox_id' => hashids_encode($transactionCloudfox->id)
                            ];

                            //(new CheckoutService())->releaseCloudfoxPaymentGetnet($data);
                        }
                    }
                }else {
                    $orderId = $list_transaction->summary->order_id;
                    $sale = Sale::where('gateway_order_id', $orderId)->first();
                    if (isset($list_transaction)) {
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
                        report(new Exception($errorGetnet));
                    }

                    $this->tryFixGatewayOrderIdAndGatewayTransactionId($sale);
                }
            }
        }

//        $transactions = Transaction::with('sale', 'company')
//            ->whereHas('sale', function($query) {
//                $query->where('owner_id', 'user_id');
//        } )
//        ->where('status_enum', Transaction::STATUS_PAID)
//        ->whereIn('gateway_id', [Gateway::GETNET_SANDBOX_ID, Gateway::GETNET_PRODUCTION_ID])
//        //->where('created_at', '=', Carbon::now()->format('Y-m-d'))
//        ->where('created_at', '>',  '2021-08-30 15:41:28')
//        ->orderBy('id');
//
//        $company = Company::find(2);
//
//        $transactions->chunk(200, function ($transactions) use($company) {
//            $getnetService = new GetnetBackOfficeService();
//            foreach ($transactions as $transaction) {
//
//                $sale = $transaction->sale;
//                $orderId = $sale->gateway_order_id;
//
//                $response = $getnetService
//                    ->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
//                    ->setStatementSubSellerId(CompanyService::getSubsellerId($company))
//                    ->getStatement();
//
//                $gatewaySale = json_decode($response);
//
//                if (
//                    isset($gatewaySale->list_transactions) &&
//                    isset($gatewaySale->list_transactions[0]) &&
//                    isset($gatewaySale->list_transactions[0]->details) &&
//                    isset($gatewaySale->list_transactions[0]->details[0]) &&
//                    isset($gatewaySale->list_transactions[0]->details[0]->release_status)
//                ) {
//                    foreach ($gatewaySale->list_transactions[0]->details as $detail) {
//                        if ($detail->release_status == 'N' and $detail->transaction_sign = '+') {
//                            $transactionCloudfox = TransactionCloudfox::create(
//                                [
//                                    'sale_id' => $sale->id,
//                                    'gateway_id' => $transaction->gateway_id,
//                                    'company_id' => $company->id,
//                                    'user_id' => $transaction->user_id,
//                                    'value' => $detail->subseller_rate_amount,
//                                    'status' => 'paid',
//                                    'status_enum' => 2,
//                                ]
//                            );
//
//                            (new CheckoutService())->releaseCloudfoxPaymentGetnet($event->transactionId);
//                        } else {
//                            if (isset($gatewaySale->list_transactions)) {
//                                $errorGetnet = 'Erro na estrutura da venda da Getnet. $sale->id = ' . $sale->id . ' $orderId = ' . $orderId;
//
//                                if (count($gatewaySale->list_transactions) == 0) {
//                                    $response = $getnetService
//                                        ->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
//                                        ->getStatement();
//
//                                    $gatewaySale = json_decode($response);
//
//                                    if (
//                                        isset($gatewaySale->list_transactions)
//                                        && isset($gatewaySale->list_transactions[0])
//                                        && isset($gatewaySale->list_transactions[0]->summary)
//                                        && isset($gatewaySale->list_transactions[0]->summary->reason_message)
//                                        && $gatewaySale->list_transactions[0]->summary->reason_message == 'CANCELADA NAO CONFIRMADA'
//                                    ) {
//                                        $errorGetnet = 'Venda na Getnet está como "CANCELADA NAO CONFIRMADA". $sale->id = ' . $sale->id . ' $orderId = ' . $orderId;
//                                    }
//                                }
//                                $this->warn($errorGetnet);
//                                report(new Exception($errorGetnet));
//                            }
//                            $this->tryFixGatewayOrderIdAndGatewayTransactionId($sale);
//                        }
//                    }
//                }
//            }
//        });
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
