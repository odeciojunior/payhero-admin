<?php

namespace App\Console\Commands;

use App\Jobs\ImportShopifyTrackingCodesJob;
use Carbon\Carbon;
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
        // xGWQmlo3 , VZp9onjG, VgdbRPbZ, K3LKQeVg
        $saleIdDecoded = hashids_decode('VgdbRPbZ  ', 'sale_id');
        $sale = Sale::find($saleIdDecoded);

        //$toDay = Carbon::now()->format("Y-m-d");

        dd($sale->payment_method == Sale::BILLET_PAYMENT  || ($sale->payment_method == Sale::PIX_PAYMENT and Carbon::now()->diffInDays($sale->end_date) > 90));

        dump(Carbon::now()->diffInDays($sale->end_date));

        dd(Carbon::now()->diffInDays($sale->end_date) < 90);

        dd($sale->payment_method == Sale::BILLET_PAYMENT  || ($sale->payment_method == Sale::PIX_PAYMENT and Carbon::now()->diffInDays($sale->end_date) > 90));
        $diffInDays = Carbon::now()->diffInDays($sale->end_date);
        if(($sale->payment_method == Sale::PIX_PAYMENT) and (Carbon::now()->diffInDays($sale->end_date) > 90)) {
            dd('mais que 90 dias');
        }

        dd($sale);
        //$toDay = Carbon::now()->format("Y-m-d");
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
