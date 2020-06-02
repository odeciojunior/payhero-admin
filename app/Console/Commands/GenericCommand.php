<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\SmsService;
use Modules\Core\Services\TrackingService;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generic';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $productService = new ProductService();
        $trackingService = new TrackingService();
        $smsService = new SmsService();

        $shopifyStores = [];

        $salesQuery = Sale::with([
            'project',
            'productsPlansSale',
        ])->doesntHave('tracking')
            ->whereHas('project', function ($query) {
                $query->whereNotNull('shopify_id');
            })
            ->where('status', 1)
            ->where('owner_id', 557)
            ->whereNotNull('shopify_order')
            ->orderByDesc('id');

        $total = $salesQuery->count();
        $count = 0;
        $added = 0;

        $salesQuery->chunk(100,
            function ($sales) use ($productService, $trackingService, $shopifyStores, $total, &$count, &$added) {
                foreach ($sales as $sale) {
                    try {
                        $count += 1;
                        $this->line("Verificando venda {$count} de {$total}: {$sale->id}");

                        $project = $sale->project;

                        if (empty($shopifyStores[$project->id])) {
                            $integration = $project->shopifyIntegrations->first();
                            $shopifyService = new ShopifyService($integration->url_store, $integration->token, false);
                            $shopifyStores[$project->id] = $shopifyService;
                        } else {
                            $shopifyService = $shopifyStores[$project->id];
                        }

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
                                                    $added++;
                                                    $this->info("Trackings criado!");
                                                    $product->tracking_code = $trackingCode;
                                                    event(new TrackingCodeUpdatedEvent($sale, $tracking,
                                                        $saleProducts));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else{
                          $this->warn('Sem fulfillment!');
                        }
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
                }
            });

        $this->info("Trackings criados: {$added}");
        $smsService->sendSms('5524998345779', "ACABOU! Trackings criados: {$added}");
    }
}


