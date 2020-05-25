<?php

namespace App\Jobs;

use Illuminate\Support\Carbon;
use Modules\Core\Entities\Tracking;
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
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $project = $this->project;
        $productService = new ProductService();
        $trackingService = new TrackingService();
        $trackingModel = new Tracking();

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


        Sale::where('project_id', $project->id)
            ->with([
                'productsPlansSale',
                'plansSales.plan.project.shopifyIntegrations'
            ])->doesntHave('tracking')
            ->where('status', 1)
            ->whereNotNull('shopify_order')
            ->chunk(100, function ($sales) use ($productService, $trackingService, $trackingModel, $shopifyService) {
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

                                                //verifica se já tem uma venda nessa conta com o mesmo código de rastreio
                                                $sale = $productPlanSale->sale;
                                                $exists = $trackingModel->where('trackings.tracking_code',
                                                    $trackingCode)
                                                    ->where('sale_id', '!=', $sale->id)
                                                    ->whereHas('sale', function ($query) use ($sale) {
                                                        $query->where('owner_id', $sale->owner_id)
                                                            ->where('upsell_id', '!=', $sale->id);
                                                    })->exists();
                                                if ($exists) {
                                                    continue;
                                                }

                                                $apiResult = $trackingService->sendTrackingToApi($trackingCode);

                                                if (!empty($apiResult)) {
                                                    //verifica se a data de postagem na transportadora é menor que a data da venda
                                                    if (!empty($apiResult->origin_info)) {
                                                        $postDate = Carbon::parse($apiResult->origin_info->ItemReceived);
                                                        if ($postDate->lt($productPlanSale->created_at)) {
                                                            if (!$apiResult->already_exists) { // deleta na api caso seja recém criado
                                                                $trackingService->deleteTrackingApi($apiResult);
                                                            }
                                                            continue;
                                                        }
                                                    }

                                                    $statusEnum = $trackingService->parseStatusApi($apiResult->status);

                                                    activity()->disableLogging();
                                                    $tracking = $trackingService->createTracking($trackingCode,
                                                        $productPlanSale, $statusEnum);
                                                    activity()->enableLogging();
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
