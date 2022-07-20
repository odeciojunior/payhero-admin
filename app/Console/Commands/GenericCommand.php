<?php

namespace App\Console\Commands;

use App\Jobs\ImportShopifyTrackingCodesJob;
use Illuminate\Console\Command;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\TrackingService;

class GenericCommand extends Command
{
    protected $signature = 'generic';
    protected $description = 'Command description';

    public $shopifyService;

    public function handle()
    {
        $project = Project::find(7149);
        $integration = $project->shopifyIntegrations->first();
        $this->shopifyService = new ShopifyService($integration->url_store, $integration->token, false);

        Sale::with([
            'productsPlansSale.tracking',
            'productsPlansSale.product',
            'productsSaleApi',
        ])->whereIn('project_id', [6689, 7149])
            ->where('status', Sale::STATUS_APPROVED)
            ->whereNotNull('shopify_order')
            ->whereNotNull('delivery_id')
            ->whereHas('productsPlansSale', function ($query) {
                $query->whereDoesntHave('tracking');
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
