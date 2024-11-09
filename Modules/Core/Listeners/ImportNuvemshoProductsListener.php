<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\NuvemshopIntegration;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Events\ImportNuvemshoProductsEvent;
use Modules\Core\Services\Nuvemshop\NuvemshopAPI;

class ImportNuvemshoProductsListener
{
    public $queue = "long";

    private NuvemshopIntegration $integration;
    private NuvemshopAPI $service;

    private function createProduct($nuvemshopProduct)
    {
        $projectId = $this->integration->project_id;
        $userId = $this->integration->user_id;

        $productName =
            isset($nuvemshopProduct["name"]) && isset($nuvemshopProduct["name"]["pt"])
                ? $nuvemshopProduct["name"]["pt"]
                : "Produto sem nome";

        foreach ($nuvemshopProduct["variants"] as $variant) {
            try {
                DB::beginTransaction();

                $variantName =
                    isset($variant["values"]) && isset($variant["values"][0]) && isset($variant["values"][0]["pt"])
                        ? $variant["values"][0]["pt"]
                        : "";

                $variantImage = null;
                if (isset($variant["image_id"])) {
                    $image = $this->service->findProductImage($nuvemshopProduct["id"], $variant["image_id"]);
                    if (isset($image["src"])) {
                        $variantImage = $image["src"];
                    }
                }

                $product = Product::updateOrCreate(
                    [
                        "shopify_id" => $nuvemshopProduct["id"],
                        "shopify_variant_id" => $variant["id"],
                    ],
                    [
                        "name" => $productName,
                        "description" => $variantName,
                        "photo" => $variantImage,
                        "price" => $variant["price"],
                        "weight" => $variant["weight"],
                        "sku" => $variant["sku"],
                        "project_id" => $projectId,
                        "user_id" => $userId,
                        "active_flag" => 1,
                    ],
                );

                $plan = Plan::updateOrCreate(
                    [
                        "project_id" => $projectId,
                        "shopify_id" => $nuvemshopProduct["id"],
                        "shopify_variant_id" => $variant["id"],
                    ],
                    [
                        "name" => $productName,
                        "description" => $variantName,
                        "code" => "",
                        "price" => $variant["price"],
                        "status" => "1",
                        "active_flag" => 1,
                    ],
                );

                ProductPlan::updateOrCreate(
                    [
                        "product_id" => $product->id,
                        "plan_id" => $plan->id,
                    ],
                    [
                        "amount" => 1,
                    ],
                );

                $plan->update(["code" => hashids_encode($plan->id)]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                continue;
            }
        }
    }

    public function handle(ImportNuvemshoProductsEvent $event)
    {
        $this->integration = $event->integration;
        $this->service = new NuvemshopAPI($this->integration->store_id, $this->integration->token);

        $page = 1;
        $perPage = 100;
        $hasNextPage = true;

        while ($hasNextPage) {
            $products = $this->service->findAllProducts(["page" => $page, "per_page" => $perPage]);

            foreach ($products as $product) {
                $this->createProduct($product);
            }

            if (count($products) < $perPage) {
                $hasNextPage = false;
                continue;
            }

            $page++;
            sleep(1);
        }
    }
}
