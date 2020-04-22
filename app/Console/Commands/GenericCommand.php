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
        $project = Project::find(203);

        $shopifyIntegration = ShopifyIntegration::where('project_id', $project->id)->first();
        try {
            $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
            $storeProducts  = $shopifyService->getShopProducts();
            if (!empty($storeProducts)) {
                foreach ($storeProducts as $storeProduct) {
                    foreach ($storeProduct->getVariants() as $variant) {
                        $title       = '';
                        $description = '';

                        try {
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
                        } catch (Exception $e) {
                            //
                        }

                        $product = Product::with('productsPlans')
                                          ->where('shopify_id', $storeProduct->getId())
                                          ->where('shopify_variant_id', $variant->getId())
                                          ->where('project_id', $project->id)
                                          ->first();
                        if (!$product) {
                            $product = Product::create([
                                                           'user_id'            => 133,
                                                           'name'               => $title,
                                                           'description'        => mb_substr($description, 0, 100),
                                                           'guarantee'          => '0',
                                                           'format'             => 1,
                                                           'category_id'        => '11',
                                                           'cost'               => $shopifyService->getShopInventoryItem($variant->getInventoryItemId())
                                                                                                  ->getCost(),
                                                           'shopify'            => true,
                                                           'price'              => '',
                                                           'shopify_id'         => $storeProduct->getId(),
                                                           'shopify_variant_id' => $variant->getId(),
                                                           'sku'                => $variant->getSku(),
                                                           'project_id'         => $project->id,
                                                       ]);
                            $plan    = Plan::create([
                                                        'shopify_id'         => $storeProduct->getId(),
                                                        'shopify_variant_id' => $variant->getId(),
                                                        'project_id'         => $project->id,
                                                        'name'               => $title,
                                                        'description'        => mb_substr($description, 0, 100),
                                                        'code'               => '',
                                                        'price'              => $variant->getPrice(),
                                                        'status'             => '1',
                                                    ]);
                            $plan->update(['code' => \Vinkla\Hashids\Facades\Hashids::encode($plan->id)]);
                            ProductPlan::create([
                                                    'product_id' => $product->id,
                                                    'plan_id'    => $plan->id,
                                                    'amount'     => '1',
                                                ]);

                            $photo = '';
                            if (count($storeProduct->getVariants()) > 1) {
                                foreach ($storeProduct->getImages() as $image) {
                                    $variantIds = $image->getVariantIds();
                                    foreach ($variantIds as $variantId) {
                                        if ($variantId == $variant->getId()) {
                                            if ($image->getSrc() != '') {
                                                $photo = $image->getSrc();
                                            } else {
                                                $photo = $storeProduct->getImage()->getSrc();
                                            }
                                        }
                                    }
                                }
                            }

                            if (empty($photo)) {
                                $image = $storeProduct->getImage();
                                if (!empty($image)) {
                                    try {
                                        $photo = $image->getSrc();
                                    } catch (Exception $e) {
                                        report($e);
                                    }
                                }
                            }
                            $product->update(['photo' => $photo]);
                        }
                    }
                }
            }
            dd('Acabou');
        } catch (Exception $e) {
            //
        }
    }
}
