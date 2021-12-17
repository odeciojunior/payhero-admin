<?php

namespace App\Jobs;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Tracking;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\TrackingService;

class ImportShopifyTrackingCodesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Project $project;
    private ShopifyService $shopifyService;
    private bool $restartWebhooks;

    public function __construct(Project $project, $restartWebhooks = true)
    {
        $this->allOnQueue('low');

        $this->project = $project;
        $this->restartWebhooks = $restartWebhooks;
    }

    public function handle()
    {
        $integration = $this->project->shopifyIntegrations->first();
        $this->shopifyService = new ShopifyService($integration->url_store, $integration->token, false);

        $this->restartWebhooks();

        Sale::with([
            'productsPlansSale.tracking',
            'productsPlansSale.product',
            'productsSaleApi',
        ])->where('project_id', $this->project->id)
            ->where('status', Sale::STATUS_APPROVED)
            ->whereNotNull('shopify_order')
            ->whereNotNull('delivery_id')
            ->whereHas('productsPlansSale', function ($query) {
                $query->whereDoesntHave('tracking');
            })->whereHas('transactions', function ($query) {
                $query->where('tracking_required', true);
            })->chunk(1000, function ($sales) {
                foreach ($sales as $sale) {
                    try {
                        $fulfillments = $this->shopifyService->findFulfillments($sale->shopify_order);
                        if (!empty($fulfillments)) {
                            $this->checkFulfillment($sale, $fulfillments);
                        }
                    } catch (\Exception $e) {
                        report($e);
                    }
                }
            });
    }

    private function restartWebhooks()
    {
        if ($this->restartWebhooks) {
            $this->shopifyService->deleteShopWebhook();

            $this->shopifyService->createShopWebhook([
                "topic" => "products/create",
                "address" => 'https://sirius.cloudfox.net/postback/shopify/' . Hashids::encode($this->project->id),
                "format" => "json",
            ]);

            $this->shopifyService->createShopWebhook([
                "topic" => "products/update",
                "address" => 'https://sirius.cloudfox.net/postback/shopify/' . Hashids::encode($this->project->id),
                "format" => "json",
            ]);

            $this->shopifyService->createShopWebhook([
                "topic" => "orders/updated",
                "address" => 'https://sirius.cloudfox.net/postback/shopify/' . Hashids::encode($this->project->id) . '/tracking',
                "format" => "json",
            ]);
        }
    }

    private function checkFulfillment(Sale $sale, array $fulfillments)
    {
        $productService = new ProductService();
        $trackingService = new TrackingService();

        $saleProducts = $productService->getProductsBySale($sale);

        foreach ($fulfillments as $fulfillment) {
            $trackingCodes = $fulfillment->getTrackingNumbers();
            if (!empty($trackingCodes)) {
                $lineItems = $fulfillment->getLineItems();
                $fulfillmentWithMultipleTracking = count($trackingCodes) === count($lineItems);
                foreach ($lineItems as $key => $lineItem) {

                    $trackingCode = $fulfillmentWithMultipleTracking ? $trackingCodes[$key] :$trackingCodes[0];

                    $products = $saleProducts
                        ->where('shopify_variant_id', $lineItem->getVariantId())
                        ->where('amount', $lineItem->getQuantity());
                    if (!$products->count()) {
                        $products = $saleProducts
                            ->where('name', $lineItem->getTitle())
                            ->where('description', $lineItem->getVariantTitle())
                            ->where('amount', $lineItem->getQuantity());
                    }

                    // Camila Monteiro
                    if (!$products->count() && $sale->owner_id === 3933) {
                        $products = $saleProducts;
                    }

                    if ($products->count()) {
                        foreach ($products as $product) {
                            $trackingService->createOrUpdateTracking($trackingCode, $product->product_plan_sale_id);
                        }
                    }
                }
            }
        }
    }
}
