<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\TrackingService;

class ProcessShopifyTrackingPostbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $projectId;
    private array $postback;

    public function __construct(int $projectId, array $postback)
    {
        $this->projectId = $projectId;
        $this->postback = $postback;
        $this->allOnQueue('postback-shopify-tracking');
    }

    public function handle()
    {
        $productService = new ProductService();
        $trackingService = new TrackingService();

        if (!isset($this->postback['id'])) {
            return;
        }

        $shopifyOrder = $this->postback['id'];

        $sales = Sale::with([
            'productsPlansSale.tracking',
            'productsPlansSale.product'
        ])->where('shopify_order', $shopifyOrder)
            ->where('project_id', $this->projectId)
            ->where('status', Sale::STATUS_APPROVED)
            ->get();

        foreach ($sales as $sale) {
            try {
                $saleProducts = $productService->getProductsBySale($sale);
                foreach ($this->postback['fulfillments'] as $fulfillment) {
                    $trackingCodes = $fulfillment["tracking_numbers"];
                    if (!empty($trackingCodes)) {
                        $lineItems = $fulfillment["line_items"];
                        //verifica se tem a mesma quantidade de rastreios e trackings
                        $fulfillmentWithMultipleTracking = count($trackingCodes) == count($lineItems);
                        //percorre os produtos que vieram no postback
                        foreach ($lineItems as $key => $line_item) {
                            //se o processamento tem mais de um rastreio, pega o rastreio referente ao produto
                            if ($fulfillmentWithMultipleTracking) {
                                $trackingCode = $trackingCodes[$key];
                            } else {
                                $trackingCode = $trackingCodes[0];
                            }
                            //verifica se existem produtos na venda com mesmo variant_id e com mesma quantidade vendida
                            $products = $saleProducts->where('shopify_variant_id', $line_item["variant_id"])
                                ->where('amount', $line_item["quantity"])
                                ->where('type_enum', (new Product)->present()->getType('physical'));
                            if (!$products->count()) {
                                $products = $saleProducts
                                    ->where('name', $line_item["title"])
                                    ->where('description', $line_item["variant_title"])
                                    ->where('amount', $line_item["quantity"]);
                            }
                            if ($products->count()) {
                                foreach ($products as $product) {
                                    $trackingService->createOrUpdateTracking($trackingCode, $product->product_plan_sale_id);
                                }
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                report($e);
            }
        }
    }
}
