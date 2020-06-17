<?php

namespace App\Jobs;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
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
     * @param  Project  $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
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
            "address" => 'https://app.cloudfox.net/postback/shopify/'.Hashids::encode($project->id),
            "format" => "json",
        ]);

        $shopifyService->createShopWebhook([
            "topic" => "products/update",
            "address" => 'https://app.cloudfox.net/postback/shopify/'.Hashids::encode($project->id),
            "format" => "json",
        ]);

        $shopifyService->createShopWebhook([
            "topic" => "orders/updated",
            "address" => 'https://app.cloudfox.net/postback/shopify/'.Hashids::encode($project->id).'/tracking',
            "format" => "json",
        ]);

        Sale::with([
            'productsPlansSale' => function ($query) {
                $query->whereDoesntHave('tracking');
            }
        ])->where('project_id', $project->id)
            ->whereHas('productsPlansSale', function ($query) {
                $query->whereDoesntHave('tracking');
            })
            ->where('status', 1)
            ->whereNotNull('shopify_order')
            ->chunk(100, function ($sales) use ($productService, $trackingService, $shopifyService) {
                foreach ($sales as $sale) {
                    try {
                        $fulfillments = $shopifyService->findFulfillments($sale->shopify_order);
                        if (!empty($fulfillments)) {
                            $saleProducts = $productService->getProductsBySale($sale);
                            foreach ($fulfillments as $fulfillment) {
                                $trackingCode = $fulfillment->getTrackingNumber();
                                if (!empty($trackingCode)) {
                                    foreach ($fulfillment->getLineItems() as $lineItem) {
                                        $products = $saleProducts->where('shopify_variant_id',
                                            $lineItem->getVariantId())->where('amount', $lineItem->getQuantity());

                                        if ($products->count()) {
                                            foreach ($products as &$product) {
                                                $productPlanSale = $sale->productsPlansSale->find($product->product_plan_sale_id);

                                                $tracking = $trackingService->createOrUpdateTracking($trackingCode,
                                                    $productPlanSale);

                                                if (!empty($tracking)) {
                                                    $product->tracking_code = $trackingCode;
                                                    event(new TrackingCodeUpdatedEvent($sale, $tracking,
                                                        $saleProducts));
                                                }
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
