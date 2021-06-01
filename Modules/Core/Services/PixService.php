<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\PixExpiredEvent;

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
    public function changePixPending()
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
                        $querySale->where(
                            'created_at',
                            '<=',
                            Carbon::now()->subHour()->toDateTimeString()
                        );
                    }
                )
                ->get();

            foreach ($sales as $sale) {
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

                $pix = $sale->pixCharges->where('status', 'ATIVA')->first();

                if (!FoxUtils::isEmpty($pix)) {
                    $pix->update(['status' => 'EXPIRED']);
                }
                event(new PixExpiredEvent($sale));
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}

