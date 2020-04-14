<?php

namespace App\Console\Commands;

use Hashids\Hashids;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ShopifyService;

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

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function fixPlanProduct()
    {
        //
    }

    public function handle()
    {
        $pl  = [];
        $pls = [];
        //        $plans = Plan::doesnthave('productsPlans');
        $products = Product::where('project_id', 203);

        $shopifyIntegration = ShopifyIntegration::where('project_id', 203)->first();
        try {
            $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
            $storeProducts  = $shopifyService->getShopProducts();

            foreach ($storeProducts as $storeProduct) {

                $product = Product::where('shopify_variant_id', $storeProduct->getId())
                                  ->orderBy('id', 'DESC')->first();

                if (empty($product)) {
                    $pls[] = $storeProduct->getId();
                } else {

                    /*$productPlan = ProductPlan::where('product_id', $product->id)
                                              ->where('amount', 1)
                                              ->orderBy('id', 'ASC')
                                              ->first();

                    try {
                        $plan = Plan::find($productPlan->plan_id)->toArray();
                    } catch (Exception $e) {
                        $pl[] = $product->id;
                    }*/
                }
            }
            dd($pls);

            /*$storeProduct   = $shopifyService->getShopProduct($plan->shopify_id);*/
            /* if (empty($storeProduct)) {
                 continue;
             } else {
                 foreach ($storeProduct->getVariants() as $variant) {
                     $title       = '';
                     $description = '';

                     $description = $variant->getOption1();
                     if ($description == 'Default Title') {
                         $description = '';
                     }
                     if ($variant->getOption2() != '') {
                         $description .= ' - ' . $variant->getOption2();
                     }
                     if ($variant->getOption3() != '') {
                         $description .= ' - ' . $variant->getOption3();
                     }
                     if (empty($storeProduct->getTitle())) {
                         $title = 'Produto sem nome';
                     } else {
                         $title = mb_substr($storeProduct->getTitle(), 0, 100);
                     }

                     $product = Product::with('productsPlans')
                                       ->where('shopify_id', $storeProduct->getId())
                                       ->where('shopify_variant_id', $variant->getId())
                                       ->where('project_id', $plan->project_id)->first();
                     if (!empty($product)) {

                         $productPlan = ProductPlan::where('product_id', $product->id)
                                                   ->where('amount', 1)
                                                   ->orderBy('id', 'ASC')->first();

                         if (empty($productPlan)) {
                             ProductPlan::create([
                                                     'product_id' => $product->id,
                                                     'plan_id'    => $plan->id,
                                                     'amount'     => 1,
                                                 ]);
                         }
                     }
                 }
             }*/
        } catch (Exception $e) {
            //
        }
    }
}
