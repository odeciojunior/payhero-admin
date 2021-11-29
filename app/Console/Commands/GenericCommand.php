<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Events\PixExpiredEvent;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\WooCommerceService;
use Vinkla\Hashids\Facades\Hashids;


class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        //$this->changeAnticipation();
        //$this->changePixToCanceled();
    }

    public function changeAnticipation()
    {
        try {

            $service = new AsaasService();

            $transactions = Transaction::with('sale')
                ->whereHas('sale', function ($query)  {
                    $query->whereNull('anticipation_status');
                    $query->where('payment_method', Sale::CREDIT_CARD_PAYMENT);
                })
                ->where('gateway_id', Gateway::ASAAS_PRODUCTION_ID)
                ->whereIn('status_enum', [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                ->whereNotNull('company_id')
                ->whereBetween('release_date',  ['2021-11-01', '2021-11-24']);


            foreach ($transactions->cursor() as $transaction) {
                $sale = $transaction->sale;
                $this->line("Sale_id: ". $sale->id . ', ');
                $response = $service->makeAnticipation($sale);

                if (isset($response['status'])) {
                    $sale->update([
                                      'anticipation_status' => $response['status'],
                                      'anticipation_id' => $response['id']
                                  ]);
                }
            }

        } catch (Exception $e) {
            report($e);
        }
    }

    public function changePixToCanceled()
    {
        try {
            $sales = Sale::where(
                [
                    ['payment_method', '=', Sale::PIX_PAYMENT],
                    ['status', '=', Sale::STATUS_PENDING]
                ]
            )
            ->whereHas(
                'pixCharges',
//                function ($querySale) {
//                    $querySale->where('status', 'ATIVA');
//                    $querySale->where( 'created_at', '<=', Carbon::now()->subHour()->toDateTimeString());
//                }
            )
            ->where('created_at', '<=', Carbon::now()->subDay()->format("Y-m-d"))
            ;

            //dd(count($sales->get()));

            foreach ($sales->cursor() as $sale) {
                $this->line("Sale_id: ". $sale->id . ', ');

                //consultar na Gerencianet para ver se não foi pago
                $data = [
                    'sale_id' => Hashids::encode($sale->id)
                ];

                $responseCheckout = (new CheckoutService())->checkPaymentPix($data);


                if ($responseCheckout->status == 'success' and $responseCheckout->payment == true) {
                    $saleModel = Sale::where(
                        [
                            ['payment_method', '=', Sale::PIX_PAYMENT],
                            ['status', '=', Sale::STATUS_APPROVED],
                        ]
                    )
                        ->whereHas('customer', function($q) use($sale){
                            $q->where('document', $sale->customer->document);
                        })
                        ->whereDate('start_date', Carbon::parse($sale->start_date)->format("Y-m-d"))->first();


                    if(empty($saleModel)) {
                        report(new Exception('Generic Command - Venda paga na Gerencianet. $sale->id = ' . $sale->id . ' $gatewayTransactionId = ' . $sale->gateway_transaction_id . ' sale conflitante $saleModel = ' . $saleModel->id));
                        continue;
                    }

                }

                $sale->update(['status' => Sale::STATUS_CANCELED]);

                foreach ($sale->transactions as $transaction) {
                    $transaction->update(
                        [
                            'status' => 'canceled',
                            'status_enum' => Transaction::STATUS_CANCELED,
                        ]
                    );
                }

                SaleLog::create(
                    [
                        'status' => 'canceled',
                        'status_enum' => 5,
                        'sale_id' => $sale->id,
                    ]
                );

                if (!empty($sale->shopify_order)) {
                    try {
                        $shopifyIntegration = $sale->project->shopifyIntegrations->first();
                        if (!empty($shopifyIntegration)) {
                            $shopifyService = new ShopifyService(
                                $shopifyIntegration->url_store,
                                $shopifyIntegration->token
                            );

                            $shopifyService->cancelOrder($sale);
                        }
                    } catch (Exception $e) {
                        report($e);
                    }
                }

                if (!empty($sale->woocommerce_order)) {
                    try {
                        $integration = WooCommerceIntegration::where('project_id', $sale->project_id)->first();
                        if (!empty($integration)) {

                            $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);

                            $service->cancelOrder($sale, 'Pix');
                        }
                    } catch (Exception $e) {
                        report($e);
                    }
                }

                $pix = $sale->pixCharges->first();

                if (!FoxUtils::isEmpty($pix)) {
                    //Atualizar o e2id
                    $pix->update(['status' => 'EXPIRED']);
                }
                event(new PixExpiredEvent($sale));
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
