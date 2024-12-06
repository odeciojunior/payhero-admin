<?php

namespace Modules\Core\Services\Nuvemshop;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\NuvemshopIntegration;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\TrackingService;

class NuvemshopService
{
    private NuvemshopIntegration $integration;

    private NuvemshopAPI $api;

    public function __construct(NuvemshopIntegration $integration)
    {
        $this->integration = $integration;
        $this->api = new NuvemshopAPI($integration->store_id, $integration->token);
    }

    public function createWebhooks()
    {
        $this->deleteWebhooks();

        $url = env("APP_URL") . "/postback/nuvemshop/{$this->integration->project_id}";

        $this->api->createWebhook([
            "event" => "products/create",
            "url" => $url,
        ]);

        $this->api->createWebhook([
            "event" => "products/update",
            "url" => $url,
        ]);

        $this->api->createWebhook([
            "event" => "order/fulfilled",
            "url" => $url,
        ]);
    }

    public function deleteWebhooks()
    {
        $currentWebhooks = $this->api->findAllWebhooks();

        foreach ($currentWebhooks as $webhook) {
            $this->api->deleteWebhook($webhook["id"]);
        }
    }

    public function createProduct(array $nuvemshopProduct)
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
                    $image = $this->api->findProductImage($nuvemshopProduct["id"], $variant["image_id"]);
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

    public function updateProduct(array $nuvemshopProduct)
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
                    $image = $this->api->findProductImage($nuvemshopProduct["id"], $variant["image_id"]);
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

    public function fulfillOrder(array $nuvemshopOrder)
    {
        $trackingNumber = $nuvemshopOrder["shipping_tracking_number"];
        if (empty($trackingNumber)) {
            return;
        }

        $projectId = $this->integration->project_id;

        $sale = Sale::where("nuvemshop_order", $nuvemshopOrder["id"])
            ->where("project_id", $projectId)
            ->first();

        if (empty($sale)) {
            return;
        }

        $productsPlanSale = ProductPlanSale::where("sale_id", $sale->id)->get();
        if ($productsPlanSale->isEmpty()) {
            return;
        }

        $trackingService = new TrackingService();

        foreach ($productsPlanSale as $productPlanSale) {
            try {
                $trackingService->createOrUpdateTracking($trackingNumber, $productPlanSale->id);
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}
