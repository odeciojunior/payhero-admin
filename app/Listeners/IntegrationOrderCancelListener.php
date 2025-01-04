<?php

namespace App\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\WooCommerceService;

class IntegrationOrderCancelListener implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function handle($event): void
    {
        $sale = $event->sale;
        if ($sale->api_flag) {
            return;
        }

        //Shopify
        if (!empty($sale->shopify_order)) {
            $shopifyIntegration = ShopifyIntegration::where("project_id", $sale->project_id)->first();
            if (!empty($shopifyIntegration)) {
                $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                $shopifyService->refundOrder($sale);
                $shopifyService->saveSaleShopifyRequest();
            }
        }

        //WooCommerce
        if (!empty($sale->woocommerce_order)) {
            $integration = WooCommerceIntegration::where("project_id", $sale->project_id)->first();
            if (!empty($integration)) {
                $service = new WooCommerceService(
                    $integration->url_store,
                    $integration->token_user,
                    $integration->token_pass,
                );

                $service->cancelOrder($sale, "Estorno");
            }
        }
    }
}
