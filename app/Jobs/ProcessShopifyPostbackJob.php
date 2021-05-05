<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\TrackingService;

class ProcessShopifyPostbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $projectId;
    /**
     * @var array
     */
    private $postback;

    /**
     * Create a new job instance.
     *
     * @param  int  $projectId
     * @param  array  $postback
     */
    public function __construct(int $projectId, array $postback)
    {
        $this->projectId = $projectId;
        $this->postback = $postback;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws PresenterException
     */
    public function handle()
    {
        $salesModel = new Sale();
        $productService = new ProductService();
        $trackingService = new TrackingService();

        $projectId = $this->projectId;
        $postback = $this->postback;

        if (isset($postback['id'])) {
            $shopifyOrder = $postback['id'];

            $sales = $salesModel->with([
                'productsPlansSale',
                'productsPlansSale.product'
            ])->where('shopify_order', $shopifyOrder)
                ->where('project_id', $projectId)
                ->where('status', $salesModel->present()->getStatus('approved'))
                ->get();

            foreach ($sales as $sale) {
                try {
                    //obtem os produtos da venda
                    $saleProducts = $productService->getProductsBySale($sale);
                    foreach ($postback['fulfillments'] as $fulfillment) {
                        $trackingCode = $fulfillment["tracking_number"];
                        if (!empty($trackingCode)) {
                            //percorre os produtos que vieram no postback
                            foreach ($fulfillment["line_items"] as $line_item) {
                                //verifica se existem produtos na venda com mesmo variant_id e com mesma quantidade vendida
                                $products = $saleProducts->where('shopify_variant_id', $line_item["variant_id"])
                                    ->where('amount', $line_item["quantity"])
                                    ->where('type_enum', (new Product)->present()->getType('physical'));
                                if(!$products->count()){
                                    $products = $saleProducts
                                        ->where('name', $line_item["title"])
                                        ->where('description', $line_item["variant_title"])
                                        ->where('amount', $line_item["quantity"]);
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
                } catch (Exception $e) {
                    Log::error('Erro ao processar postback do shopify: '.$e->getMessage());
                }
            }
        }
    }
}
