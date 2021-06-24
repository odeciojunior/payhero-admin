<?php

namespace App\Jobs;

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

    private $project;

    /**
     * Create a new job instance.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->allOnQueue('long');
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $project = $this->project;
        $productService = new ProductService();
        $trackingService = new TrackingService();

        $integration = $project->shopifyIntegrations->first();
        $shopifyService = new ShopifyService($integration->url_store, $integration->token, false);

        $shopifyService->deleteShopWebhook();

        $shopifyService->createShopWebhook([
            "topic" => "products/create",
            "address" => 'https://sirius.cloudfox.net/postback/shopify/' . Hashids::encode($project->id),
            "format" => "json",
        ]);

        $shopifyService->createShopWebhook([
            "topic" => "products/update",
            "address" => 'https://sirius.cloudfox.net/postback/shopify/' . Hashids::encode($project->id),
            "format" => "json",
        ]);

        $shopifyService->createShopWebhook([
            "topic" => "orders/updated",
            "address" => 'https://sirius.cloudfox.net/postback/shopify/' . Hashids::encode($project->id) . '/tracking',
            "format" => "json",
        ]);

        Sale::with([
            'productsPlansSale.tracking',
            'productsPlansSale.product',
        ])->where('project_id', $project->id)
            ->where('status', Sale::STATUS_APPROVED)
            ->whereNotNull('shopify_order')
            ->whereNotNull('delivery_id')
            ->whereHas('productsPlansSale', function ($query) {
                $query->whereDoesntHave('tracking');
            })->whereHas('transactions', function ($query) {
                $query->where('tracking_required', true);
            })
            ->chunk(100, function ($sales) use ($productService, $trackingService, $shopifyService) {
                foreach ($sales as $sale) {
                    try {
                        $fulfillments = $shopifyService->findFulfillments($sale->shopify_order);
                        if (!empty($fulfillments)) {
                            $saleProducts = $productService->getProductsBySale($sale);
                            foreach ($fulfillments as $fulfillment) {
                                $trackingCodes = $fulfillment->getTrackingNumbers();
                                if (!empty($trackingCodes)) {
                                    $lineItems = $fulfillment->getLineItems();
                                    $fulfillmentWithMultipleTracking = count($trackingCodes) == count($lineItems);
                                    foreach ($lineItems as $key => $lineItem) {
                                        if ($fulfillmentWithMultipleTracking) {
                                            $trackingCode = $trackingCodes[$key];
                                        } else {
                                            $trackingCode = $trackingCodes[0];
                                        }
                                        $products = $saleProducts
                                            ->where('shopify_variant_id', $lineItem->getVariantId())
                                            ->where('amount', $lineItem->getQuantity());
                                        if (!$products->count()) {
                                            $products = $saleProducts
                                                ->where('name', $lineItem->getTitle())
                                                ->where('description', $lineItem->getVariantTitle())
                                                ->where('amount', $lineItem->getQuantity());
                                        }
                                        if ($products->count()) {
                                            foreach ($products as $product) {
                                                $productPlanSale = $sale->productsPlansSale->find($product->product_plan_sale_id);

                                                $trackingService->createOrUpdateTracking($trackingCode, $productPlanSale);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        report($e);
                    }
                }
            });
    }
}
