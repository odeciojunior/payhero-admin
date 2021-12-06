<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\PixExpiredEvent;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class PixService
 * @package Modules\Core\Services
 */
class PixService
{

    /**
     *  Pix Pending
     * @return void
     */
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
                function ($querySale) {
                    $querySale->where('status', 'ATIVA');
                    $querySale->where( 'created_at', '<=', Carbon::now()->subHour()->toDateTimeString());
                }
            );

            foreach ($sales->cursor() as $sale) {

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
                    ->whereDate('start_date', \Carbon\Carbon::parse($sale->start_date)->format("Y-m-d"))->first();


                    if(empty($saleModel)) {
                        report(new Exception('Venda paga na Gerencianet e com problema no pagamento. $sale->id = ' . $sale->id . ' $gatewayTransactionId = ' . $sale->gateway_transaction_id . ' sale conflitante $saleModel = ' . $saleModel->id));
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

                $pix = $sale->pixCharges->where('status', 'ATIVA')->first();

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

