<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
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
                'productsPlansSale.tracking',
                'productsPlansSale.product'
            ])->where('shopify_order', $shopifyOrder)
                ->where('project_id', $projectId)
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
                                    ->where('amount', $line_item["quantity"]);
                                if ($products->count()) {
                                    foreach ($products as &$product) {

                                        $productPlanSale = $sale->productsPlansSale->find($product->product_plan_sale_id);

                                        $tracking = $trackingService->createOrUpdateTracking($trackingCode, $productPlanSale);

                                        if(!empty($tracking)) {
                                            //atualiza no array de produtos para enviar no email
                                            $product->tracking_code = $trackingCode;
                                            event(new TrackingCodeUpdatedEvent($sale, $tracking, $saleProducts));
                                        }
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
