<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\TrackingService;
use Illuminate\Support\Facades\Log;

class ProcessWooCommercePostbackTracking implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
     * @param int $projectId
     * @param array $postback
     */
    public function __construct(int $projectId, array $postback)
    {
        $this->projectId = $projectId;
        $this->postback = $postback;
    }

    public function handle()
    {
        $productService = new ProductService();
        $trackingService = new TrackingService();

        if (!isset($this->postback["id"])) {
            return;
        }

        $order = $this->postback["id"];

        $sales = Sale::where("woocommerce_order", $order)
            ->where("project_id", $this->projectId)
            ->where("status", Sale::STATUS_APPROVED)
            ->get();

        if (empty($sales)) {
            return;
        }

        foreach ($sales as $sale) {
            try {
                $saleProducts = $productService->getProductsBySale($sale);

                foreach ($saleProducts as $product) {
                    $trackingCode = $this->postback["correios_tracking_code"];

                    if (!empty($trackingCode)) {
                        $trackingService->createOrUpdateTracking($trackingCode, $product->product_plan_sale_id);
                    }
                }

                // foreach ($this->postback['line_items'] as $line_item) {
                //     $trackingCode = $this->postback["correios_tracking_code"];
                //     if (!empty($trackingCode)) {

                //         //verifica se existem produtos na venda com mesmo variant_id e com mesma quantidade vendida
                //         $products = $saleProducts->where('shopify_variant_id', $line_item["sku"])
                //             ->where('amount', $line_item["quantity"])
                //             ->where('type_enum', (new Product)->present()->getType('physical'));

                //         if (!$products->count()) {
                //             $products = $saleProducts
                //                 ->where('name', $line_item["name"])
                //                 ->where('amount', $line_item["quantity"]);
                //         }
                //         if ($products->count()) {
                //             foreach ($products as $product) {
                //                 $trackingService->createOrUpdateTracking($trackingCode, $product->product_plan_sale_id);
                //             }
                //         }
                //     }
                // }
            } catch (Exception $e) {
                report($e);
            }
        }
    }
}
