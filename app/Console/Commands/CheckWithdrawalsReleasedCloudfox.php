<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\TransactionCloudfox;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;
use Vinkla\Hashids\Facades\Hashids;

class CheckWithdrawalsReleasedCloudfox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getnet:check-withdrawals-released-cloudfox';

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

        try {

            $query = "select t.id as transaction_id, t.gateway_id, t.user_id, t.status, t.status_enum, s.id as sale_id, s.gateway_id,
             s.owner_id, s.created_at, tc.id as transaction_cloudfox_id, tc.release_date, tc.gateway_released_at
            from transactions as t  inner join sales as s on s.id = t.sale_id
            left join transaction_cloudfox as tc on s.id = tc.sale_id
            where s.created_at >= '2021-07-30 15:41:28' and s.gateway_id = 15 and s.owner_id = t.user_id
                        and (t.status_enum = 2 or t.status_enum = 1)
                        and tc.gateway_released_at is null
                        and t.deleted_at IS NULL
            order by s.id asc ";


            $dbResults = DB::select($query);

            $total = count($dbResults);
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $getnetService = new GetnetBackOfficeService();
            $company = Company::find(2);
            $aux = 0;

            foreach ($dbResults as $dbResult) {
                $this->line($dbResult->sale_id);

                if ($aux == 100) {
                    $getnetService = new GetnetBackOfficeService();
                    $aux = 0;
                }
                $aux++;

                $transaction = Transaction::with('sale')->find($dbResult->transaction_id);
                $sale = $transaction->sale;
                $orderId = $sale->gateway_order_id;

                $response = $getnetService
                    ->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                    ->setStatementSubSellerId(CompanyService::getSubsellerId($company))
                    ->getStatement();

                $gatewaySale = json_decode($response);

                if (
                    isset($gatewaySale->list_transactions) &&
                    isset($gatewaySale->list_transactions[0]) &&
                    isset($gatewaySale->list_transactions[0]->details) &&
                    isset($gatewaySale->list_transactions[0]->details[0]) &&
                    isset($gatewaySale->list_transactions[0]->details[0]->release_status)
                ) {
                    foreach ($gatewaySale->list_transactions[0]->details as $detail) {
                        if ($detail->release_status == 'N' and $detail->transaction_sign = '+') {

                            $transactionCloudfox = TransactionCloudfox::where('sale_id', $sale->id)->first();
                            if (empty($transactionCloudfox)) {
                                    $transactionCloudfox = TransactionCloudfox::create(
                                        [
                                            'sale_id' => $sale->id,
                                            'gateway_id' => $transaction->gateway_id,
                                            'company_id' => $company->id,
                                            'user_id' => $company->user_id,
                                            'value' => $detail->subseller_rate_amount,
                                            'value_total' => $detail->installment_amount,
                                            'status' => 'paid',
                                            'status_enum' => 2,
                                            'release_date' => now()->format('Y-m-d')
                                        ]
                                    );
                            }

                            if (!foxutils()->isProduction()) {
                                $this->line('Sale id: ' .  $sale->id . ', Transaction id: ' . $transaction->id . ', Transaction Cloudfox id: ' . $transactionCloudfox->id );
                            }

                            if (!empty($transactionCloudfox->release_date)) {
                                $data = [
                                    'transaction_cloudfox_id' => Hashids::encode($transactionCloudfox->id)
                                ];

                            $responseCheckout = (new CheckoutService())->releaseCloudfoxPaymentGetnet($data);
                            //dd($responseCheckout);
                            }
                        }
                    }
                }else {
                    if (isset($gatewaySale->list_transactions)) {
                        $errorGetnet = 'Comissão da cloudfox, erro na estrutura da venda da Getnet. $sale->id = ' . $sale->id . ' $orderId = ' . $orderId;

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
                                $errorGetnet = 'Comissão da cloudfox, venda na Getnet está como "CANCELADA NAO CONFIRMADA". $sale->id = ' . $sale->id . ' $orderId = ' . $orderId;
                            }
                        }
                        if (!foxutils()->isProduction()) {
                            $this->warn($errorGetnet);
                            //report(new Exception($errorGetnet));
                        }
                    }

                    $this->tryFixGatewayOrderIdAndGatewayTransactionId($sale);
                }
                $bar->advance();
            }

            $bar->finish();
        } catch ( Exception $e) {
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
