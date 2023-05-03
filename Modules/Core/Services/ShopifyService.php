<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleShopifyRequest;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\User;
use Modules\Core\Events\ShopifyIntegrationReadyEvent;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use PHPHtmlParser\Exceptions\UnknownChildTypeException;
use PHPHtmlParser\Selector\Parser;
use PHPHtmlParser\Selector\Selector;
use Slince\Shopify\Client;
use Slince\Shopify\Manager\Asset\Asset;
use Slince\Shopify\Manager\ProductImage\Image;
use Slince\Shopify\Manager\ProductVariant\Variant;
use Slince\Shopify\Manager\Theme\Theme;
use Slince\Shopify\Manager\Webhook\Webhook;
use Slince\Shopify\PublicAppCredential;

class ShopifyService
{
    public ShopifyTemplateService $templateService;

    private $cacheDir;

    private $credential;

    private $client;

    private $saleId;

    private $sendData = [];

    private $receivedData = [];

    private $exceptions = [];

    private $method;

    private $urlStore;

    private $project = "admin";

    public function __construct(string $urlStore, string $token, $getThemes = true)
    {
        if (!$this->cacheDir) {
            $cache = "/var/tmp";
            //$cache = storage_path();
        } else {
            $cache = $this->cacheDir;
        }

        $this->templateService = new ShopifyTemplateService($urlStore, $token, $getThemes);

        $this->credential = new PublicAppCredential($token);
        $this->client = new Client($urlStore, $this->credential, [
            "meta_cache_dir" => $cache, // Metadata cache dir, required
        ]);

        $this->urlStore = $urlStore;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function importShopifyProduct($projectId, $userId, $shopifyProductId): bool
    {
        try {
            $storeProduct = $this->getShopProduct($shopifyProductId);
            sleep(1);

            if (empty($storeProduct)) {
                return false;
            }

            $notazzConfig = Project::select("notazz_configs")->find($projectId)->notazz_configs ?? null;
            $updateCostShopify = !is_null($notazzConfig) ? json_decode($notazzConfig) : null;

            $products = Product::with("productsPlans.plan")
                ->where("project_id", $projectId)
                ->get();

            $statusProductShopify = 1;
            try {
                $result = $this->client->createRequest(
                    "GET",
                    "https://{$this->urlStore}/admin/api/2022-04/products/{$shopifyProductId}.json"
                );

                if (
                    !empty($result) &&
                    isset($result["product"]["status"]) &&
                    $result["product"]["status"] != "active"
                ) {
                    $statusProductShopify = 0;
                }
            } catch (Exception $e) {
                report($e);
            }

            $productsArray = [];
            foreach ($storeProduct->getVariants() as $variant) {
                $title = "";
                $description = "";

                try {
                    $description = $variant->getOption1();
                    if ($description == "Default Title") {
                        $description = "";
                    }
                    if ($variant->getOption2() != "") {
                        $description .= " - " . $variant->getOption2();
                    }
                    if ($variant->getOption3() != "") {
                        $description .= " - " . $variant->getOption3();
                    }
                    if (empty($storeProduct->getTitle())) {
                        $title = "Produto sem nome";
                    } else {
                        $title = mb_substr($storeProduct->getTitle(), 0, 100);
                    }
                } catch (Exception $e) {
                    //
                }
                $product = $products
                    ->where("shopify_id", $storeProduct->getId())
                    ->where("shopify_variant_id", $variant->getId())
                    ->where("project_id", $projectId)
                    ->first();
                if ($product) {
                    $productsArray[] = $product->id;
                    $product->fill([
                        "name" => $title,
                        "description" => mb_substr($description, 0, 100),
                        "weight" => $variant->getWeight(),
                        "shopify_id" => $storeProduct->getId(),
                        "shopify_variant_id" => $variant->getId(),
                        "sku" => $variant->getSku(),
                        "project_id" => $projectId,
                        "active_flag" => $statusProductShopify,
                    ]);

                    $productPlan = $product->productsPlans
                        ->where("amount", 1)
                        ->sortBy("id")
                        ->first();
                    if (!empty($productPlan)) {
                        if (($updateCostShopify->update_cost_shopify ?? 0) == 1) {
                            $costProduct = $this->getCostShopify($variant);
                            if ($costProduct !== "") {
                                $productPlan->fill(["cost" => $costProduct * 100]);
                                if ($productPlan->isDirty()) {
                                    $productPlan->save();
                                }
                            }
                        }

                        $plan = $productPlan->plan;
                        $plan->fill([
                            "name" => $title,
                            "description" => mb_substr($description, 0, 100),
                            "price" => $variant->getPrice(),
                            "status" => "1",
                            "active_flag" => $statusProductShopify,
                            "project_id" => $projectId,
                            "name" => $title,
                            "description" => mb_substr($description, 0, 100),
                            "price" => $variant->getPrice(),
                            "status" => "1",
                            "active_flag" => $statusProductShopify,
                            "project_id" => $projectId,
                        ]);
                        if ($plan->isDirty()) {
                            $plan->save();
                        }

                        $photo = "";
                        if (count($storeProduct->getVariants()) > 1) {
                            foreach ($storeProduct->getImages() as $image) {
                                $variantIds = $image->getVariantIds();
                                foreach ($variantIds as $variantId) {
                                    if ($variantId == $variant->getId()) {
                                        if ($image->getSrc() != "") {
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
                        $product->fill(["photo" => $photo]);

                        if ($product->isDirty()) {
                            $product->save();
                        }
                    } else {
                        $plan = Plan::create([
                            "shopify_id" => $storeProduct->getId(),
                            "shopify_variant_id" => $variant->getId(),
                            "project_id" => $projectId,
                            "name" => $title,
                            "description" => mb_substr($description, 0, 100),
                            "code" => "",
                            "price" => $variant->getPrice() > 100000 ? 100 : $variant->getPrice(),
                            "status" => "1",
                            "active_flag" => $statusProductShopify,
                        ]);

                        $dataProductPlan = [
                            "product_id" => $product->id,
                            "plan_id" => $plan->id,
                            "amount" => 1,
                        ];
                        if (($updateCostShopify->update_cost_shopify ?? 0) == 1) {
                            $costShopify = $this->getCostShopify($variant);
                            if ($costShopify !== "") {
                                $dataProductPlan["cost"] = $costShopify * 100;
                            }
                        }

                        ProductPlan::create($dataProductPlan);

                        $plan->update(["code" => hashids_encode($plan->id)]);
                    }
                } else {
                    $product = Product::create([
                        "user_id" => $userId,
                        "name" => $title,
                        "description" => mb_substr($description, 0, 100),
                        "guarantee" => "0",
                        "format" => 1,
                        "category_id" => "11",
                        "shopify" => true,
                        "price" => "",
                        "shopify_id" => $storeProduct->getId(),
                        "shopify_variant_id" => $variant->getId(),
                        "sku" => $variant->getSku(),
                        "project_id" => $projectId,
                        "active_flag" => $statusProductShopify,
                    ]);

                    $productsArray[] = $product->id;
                    $plan = Plan::create([
                        "shopify_id" => $storeProduct->getId(),
                        "shopify_variant_id" => $variant->getId(),
                        "project_id" => $projectId,
                        "name" => $title,
                        "description" => mb_substr($description, 0, 100),
                        "code" => "",
                        "price" => $variant->getPrice() > 100000 ? 100 : $variant->getPrice(),
                        "status" => "1",
                        "active_flag" => $statusProductShopify,
                    ]);
                    $plan->update(["code" => hashids_encode($plan->id)]);

                    $dataProductPlan = [
                        "product_id" => $product->id,
                        "plan_id" => $plan->id,
                        "amount" => "1",
                    ];
                    if (($updateCostShopify->update_cost_shopify ?? 0) == 1) {
                        $costShopify = $this->getCostShopify($variant);
                        if ($costShopify !== "") {
                            $dataProductPlan["cost"] = $costShopify * 100;
                        }
                    }

                    ProductPlan::create($dataProductPlan);

                    $photo = "";
                    if (count($storeProduct->getVariants()) > 1) {
                        foreach ($storeProduct->getImages() as $image) {
                            $variantIds = $image->getVariantIds();
                            foreach ($variantIds as $variantId) {
                                if ($variantId == $variant->getId()) {
                                    if ($image->getSrc() != "") {
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
                    $product->update(["photo" => $photo]);
                }
            }

            $products = Product::where("project_id", $projectId)
                ->where("shopify_id", $shopifyProductId)
                ->whereNotIn("id", collect($productsArray))
                ->get();

            if ($products->count()) {
                $productIds = $products->pluck("id");

                $plans = Plan::select(DB::raw("plans.*"))
                    ->with([
                        "plansSales",
                        "affiliateLinks",
                        "productsPlans" => function ($query) use ($productIds) {
                            $query->whereIn("product_id", $productIds);
                        },
                    ])
                    ->join("products_plans", "products_plans.plan_id", "=", "plans.id")
                    ->whereNull("products_plans.deleted_at")
                    ->whereIn("products_plans.product_id", $productIds)
                    ->get();

                $arrayDelete = [];
                foreach ($plans as $plan) {
                    if (count($plan->plansSales) == 0) {
                        $productPlans = $plan->productsPlans;
                        $othersProducts = $productPlans->whereNotIn("product_id", $productIds);
                        if (count($othersProducts) == 0) {
                            foreach ($plan->productsPlans as $productPlan) {
                                $arrayDelete[] = $productPlan->product_id;
                                $productPlan->delete();
                            }
                            if (count($plan->affiliateLinks) > 0) {
                                foreach ($plan->affiliateLinks as $affiliateLink) {
                                    $affiliateLink->delete();
                                }
                            }
                            $plan->delete();
                        }
                    }
                }

                Product::whereIn("products.id", collect($arrayDelete))
                    ->leftJoin("products_plans as pp", function ($join) {
                        $join->on("pp.product_id", "=", "products.id")->whereNull("pp.deleted_at");
                    })
                    ->whereNull("pp.id")
                    ->delete();
            }

            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function importShopifyStore($projectId, $userId)
    {
        ShopifyIntegration::where("project_id", $projectId)->update([
            "status" => ShopifyIntegration::STATUS_PENDING,
        ]);

        $project = Project::find($projectId);

        $pagination = $this->getShopProducts();
        sleep(1);
        $storeProducts = $pagination->current();
        sleep(1);

        $nextPagination = true;

        while ($nextPagination) {
            foreach ($storeProducts as $shopifyProduct) {
                try {
                    $this->importShopifyProduct($projectId, $userId, $shopifyProduct->getId());
                } catch (Exception $e) {
                    report($e);
                }
            }

            if ($pagination->hasNext()) {
                sleep(1);
                $nextPageInfo = $pagination->getNextPageInfo();
                sleep(1);
                $storeProducts = $pagination->current($nextPageInfo);
            } else {
                $nextPagination = false;
            }
        }

        if (FoxUtils::isProduction()) {
            $this->createShopifyIntegrationWebhook($projectId, "https://admin.nexuspay.vip/postback/shopify/");
        }

        $user = User::find($userId);
        if (!empty($project) && !empty($user)) {
            if (FoxUtils::isProduction()) {
                event(new ShopifyIntegrationReadyEvent($user, $project));
            }

            ShopifyIntegration::where("project_id", $projectId)->update([
                "status" => ShopifyIntegration::STATUS_APPROVED,
            ]);
        }
    }

    /**
     * @param $projectId
     * @param $url
     * @return bool
     * @throws Exception
     */
    public function createShopifyIntegrationWebhook($projectId, $url)
    {
        $postbackUrl = $url;

        $this->deleteShopWebhook();

        $this->createShopWebhook([
            "topic" => "products/create",
            "address" => $postbackUrl . hashids_encode($projectId),
            "format" => "json",
        ]);

        $this->createShopWebhook([
            "topic" => "products/update",
            "address" => $postbackUrl . hashids_encode($projectId),
            "format" => "json",
        ]);

        $this->createShopWebhook([
            "topic" => "orders/updated",
            "address" => $postbackUrl . hashids_encode($projectId) . "/tracking",
            "format" => "json",
        ]);

        return true;
    }

    /**
     * @return string
     */
    public function getShopName()
    {
        if (!empty($this->client)) {
            return $this->client
                ->getShopManager()
                ->get()
                ->getName();
        }

        return "";
    }

    /**
     * @return string
     */
    public function getShopUrl()
    {
        if (!empty($this->client)) {
            return "https://" .
                $this->client
                    ->getShopManager()
                    ->get()
                    ->getDomain();
        }

        return "";
    }

    /**
     * @return string
     */
    public function getShopDomain()
    {
        if (!empty($this->client)) {
            return $this->client
                ->getShopManager()
                ->get()
                ->getDomain();
        }

        return "";
    }

    /**
     * @return int|string
     */
    public function getShopId()
    {
        if (!empty($this->client)) {
            return $this->client
                ->getShopManager()
                ->get()
                ->getId();
        }

        return "";
    }

    /**
     * @return array
     */
    public function getShopProducts()
    {
        if (!empty($this->client)) {
            return $this->client->getProductManager()->paginate(["limit" => 250]);
        }

        return [];
    }

    /**
     * @param $variantId
     * @return \Slince\Shopify\Manager\Product\Product
     */
    public function getShopProduct($variantId)
    {
        try {
            if (!empty($this->client)) {
                return $this->client->getProductManager()->find($variantId);
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getShopInventoryItem($shopifyItemId)
    {
        if (empty($this->client) || empty($shopifyItemId)) {
            return [];
        }

        try {
            return $this->client->getInventoryItemManager()->find($shopifyItemId);
        } catch (Exception $e) {
            if (method_exists($e, "getCode") && $e->getCode() == 429) {
                sleep(1);
                try {
                    return $this->client->getInventoryItemManager()->find($shopifyItemId);
                } catch (Exception $e) {
                    return [];
                }
            }

            return [];
        }
    }

    /**
     * @param array $data
     * @return Webhook|null
     */
    public function createShopWebhook($data = [])
    {
        try {
            if (!empty($this->client)) {
                return $this->client->getWebhookManager()->create($data);
            } else {
                return null;
            }
        } catch (Exception $e) {
            if (method_exists($e, "getCode") && in_array($e->getCode(), [401, 402, 403, 404, 406, 422, 423, 429])) {
                return null;
            }
            throw $e;
        }
    }

    public function getShopWebhook($webhookId = null)
    {
        try {
            if (empty($this->client)) {
                return [];
            }

            if (!empty($webhookId)) {
                return $this->client->getWebhookManager()->find($webhookId);
            }

            return $this->client->getWebhookManager()->findAll();
        } catch (Exception $e) {
            if (method_exists($e, "getCode") && in_array($e->getCode(), [401, 402, 403, 404, 406, 423, 429, 503])) {
                return [];
            }

            report($e);
            return [];
        }
    }

    public function deleteShopWebhook($webhookId = null)
    {
        try {
            if (empty($this->client)) {
                return [];
            }

            if (!empty($webhookId)) {
                return $this->client->getWebhookManager()->remove($webhookId);
            }

            $webhooks = $this->getShopWebhook();
            foreach ($webhooks as $webhook) {
                $this->client->getWebhookManager()->remove($webhook->getId());
            }

            // return $this->client->getWebhookManager()->findAll();
            return [];
        } catch (Exception $e) {
            if (method_exists($e, "getCode") && in_array($e->getCode(), [401, 402, 403, 404, 406, 423, 429, 503])) {
                return [];
            }
            throw $e;
        }
    }

    /**
     * @param null $variantId
     * @return Variant|null
     */
    public function getProductVariant($variantId = null)
    {
        if (!empty($this->client)) {
            if ($variantId) {
                return $this->client->getProductVariantManager()->find($variantId);
            }
        } else {
            return null;
        }
    }

    /**
     * @param null $productId
     * @return \Slince\Shopify\Manager\Product\Product|null
     */
    public function getProduct($productId = null)
    {
        if (!empty($this->client)) {
            if ($productId) {
                return $this->client->getProductManager()->find($productId);
            }
        } else {
            return null;
        }
    }

    /**
     * @param null $productId
     * @param null $imageId
     * @return Image|null
     */
    public function getImage($productId = null, $imageId = null)
    {
        if (!empty($this->client)) {
            if ($productId && $imageId) {
                return $this->client->getProductImageManager()->find($productId, $imageId);
            }
        } else {
            return null;
        }
    }

    /**
     * @param Sale $sale
     * @return array
     * @throws PresenterException
     */
    public function prepareOrder(Sale $sale)
    {
        $this->saleId = $sale->id;
        $delivery = $sale->delivery;
        $client = $sale->customer;

        $totalValue = $sale->present()->getSubTotal();

        $firstProduct = true;
        $items = [];
        foreach ($sale->productsPlansSale as $productsPlanSale) {
            $productPrice = 0;
            if ($firstProduct) {
                if (!empty($sale->shopify_discount)) {
                    $totalValue -= preg_replace("/[^0-9]/", "", $sale->shopify_discount);
                }

                if (!empty($sale->automatic_discount)) {
                    $totalValue -= preg_replace("/[^0-9]/", "", $sale->automatic_discount);
                }

                if ($productsPlanSale->amount > 1) {
                    $productPrice = intval($totalValue / $productsPlanSale->amount);
                } else {
                    $productPrice = $totalValue;
                }
                $productPrice = substr_replace($productPrice, ".", strlen($productPrice) - 2, 0);
                $firstProduct = false;
            }

            $product = $productsPlanSale->product;

            $items[] = [
                "grams" => 500,
                "id" => $productsPlanSale->plan->id,
                "price" => $productPrice,
                "product_id" => $product->shopify_id,
                "quantity" => $productsPlanSale->amount,
                "requires_shipping" => true,
                "sku" => $product->sku,
                "title" => $product->name,
                "variant_id" => $product->shopify_variant_id,
                "variant_title" => $product->description,
                "name" => $product->name,
                "gift_card" => false,
            ];
        }

        $address = "-";
        $shippingAddress = [];
        if (!empty($delivery)) {
            $address = $delivery->street . " - " . $delivery->number;

            if (!empty($delivery->complement)) {
                $address .= " - " . $delivery->complement;
            }
            $address .= " - " . $delivery->neighborhood;

            $shippingAddress = [
                "address1" => $address,
                "address2" => "",
                "city" => $delivery->city ?? "-",
                "company" => $client->document,
                "country" => "Brasil",
                "first_name" => empty($delivery)
                    ? $client->present()->getFirstName()
                    : $delivery->present()->getReceiverFirstName(),
                "last_name" => empty($delivery)
                    ? $client->present()->getLastName()
                    : $delivery->present()->getReceiverLastName(),
                "phone" => $client->present()->getTelephoneShopify(),
                "province" => $delivery->state ?? "-",
                "zip" => empty($delivery) ? "-" : FoxUtils::formatCEP($delivery->zip_code),
                "name" => $client->name,
                "country_code" => "BR",
                "province_code" => $delivery->state ?? "-",
            ];
        }

        // Endereço de Faturamento
        $billingAddress = [
            "first_name" => empty($delivery)
                ? $client->present()->getFirstName()
                : $delivery->present()->getReceiverFirstName(),
            "last_name" => empty($delivery)
                ? $client->present()->getLastName()
                : $delivery->present()->getReceiverLastName(),
            "address1" => $address,
            "phone" => $client->present()->getTelephoneShopify(),
            "city" => $delivery->city ?? "-",
            "province" => $delivery->state ?? "-",
            "country" => "Brasil",
            "zip" => empty($delivery) ? "-" : FoxUtils::formatCEP($delivery->zip_code),
        ];

        $shippingValue = intval(preg_replace("/[^0-9]/", "", $sale->shipment_value));
        if ($shippingValue <= 0) {
            $shippingTitle = "Frete Grátis para Todo Brasil";
        } else {
            $shippingTitle = "Standard Shipping";
            $totalValue += $shippingValue;
        }
        $shipping[] = [
            "custom" => true,
            "price" => $shippingValue <= 0 ? 0.0 : substr_replace($shippingValue, ".", strlen($shippingValue) - 2, 0),
            "title" => $shippingTitle,
        ];

        $orderData = [
            "accepts_marketing" => false,
            "currency" => "BRL",
            "email" => $client->email,
            "phone" => $client->present()->getTelephoneShopify(),
            "first_name" => empty($delivery)
                ? $client->present()->getFirstName()
                : $delivery->present()->getReceiverFirstName(),
            "last_name" => empty($delivery)
                ? $client->present()->getLastName()
                : $delivery->present()->getReceiverLastName(),
            "buyer_accepts_marketing" => false,
            "line_items" => $items,
            "shipping_address" => $shippingAddress,
            "billing_address" => $billingAddress,
            "shipping_lines" => $shipping,
            "note_attributes" => [
                "token_nexuspay" => hashids_encode($sale->checkout_id),
            ],
            "total_price" => substr_replace($totalValue, ".", strlen($totalValue) - 2, 0),
        ];

        if ($sale->payment_method == Sale::CREDIT_CARD_PAYMENT) {
            //cartao

            $orderData += [
                "transactions" => [
                    [
                        "gateway" => "nexuspay",
                        "authorization" => hashids_encode($sale->id, "sale_id"),
                        "kind" => "sale",
                        "status" => "success",
                        "amount" => foxutils()->floatFormat($totalValue),
                    ],
                ],
            ];
        } else {
            if ($sale->payment_method == Sale::BILLET_PAYMENT || $sale->payment_method == Sale::PIX_PAYMENT) {
                //boleto

                $orderData += [
                    "financial_status" => $sale->status == 1 ? "paid" : "pending",
                    "transactions" => [
                        [
                            "gateway" => "nexuspay",
                            "authorization" => hashids_encode($sale->id, "sale_id"),
                            "kind" => "sale",
                            "status" => $sale->status == 1 ? "success" : "pending",
                            "amount" => foxutils()->floatFormat($totalValue),
                        ],
                    ],
                ];
            } else {
                return null;
            }
        }

        return $orderData;
    }

    /**
     * @param Sale $sale
     * @return array
     */
    public function newOrder(Sale $sale)
    {
        if (is_null($sale->upsell_id)) {
            if ($sale->upsells->count()) {
                return $this->addItemsToOrder($sale->id);
            } else {
                return $this->createOrder($sale);
            }
        } else {
            return $this->addItemsToOrder($sale->upsell_id);
        }
    }

    /**
     * @param Sale $sale
     * @return array
     */
    public function createOrder(Sale $sale)
    {
        try {
            $this->method = __METHOD__;

            $sale = Sale::find($sale->id);

            if (!empty($sale->shopify_order)) {
                return [
                    "status" => "error",
                    "message" => "Venda já existe no shopify.",
                ];
            }

            $orderData = $this->prepareOrder($sale);

            if (empty($orderData)) {
                return [
                    "status" => "error",
                    "message" => "Venda não atende requisitos para gerar ordem no shopify.",
                ];
            }

            $this->sendData = $orderData;

            $order = $this->client->post("orders", [
                "order" => $orderData,
            ]);

            $this->receivedData = $order;

            if (FoxUtils::isEmpty($order["order"]["id"])) {
                return [
                    "status" => "error",
                    "message" => "Error ao tentar gerar ordem no shopify.",
                ];
            }
            $sale->update([
                "shopify_order" => $order["order"]["id"],
            ]);

            return [
                "status" => "success",
                "message" => "Ordem gerada com sucesso.",
            ];
        } catch (Exception $e) {
            $this->exceptions[] = $e->getMessage();
            report($e);

            $shippingAddress["phone"] = "+5555959844325";
            $orderData["phone"] = "+5555959844325";
            $orderData["shipping_address"] = $shippingAddress;

            $this->sendData = $orderData;

            $order = $this->client->post("orders", [
                "order" => $orderData,
            ]);

            $this->receivedData = $order;

            if (FoxUtils::isEmpty($order["order"]["id"])) {
                return [
                    "status" => "error",
                    "message" => "Error ao tentar gerar ordem no shopify.",
                ];
            }
            $sale->update([
                "shopify_order" => $order["order"]["id"],
            ]);

            return [
                "status" => "success",
                "message" => "Ordem gerada com sucesso.",
            ];
        }
    }

    /**
     * Usado para gerar order de vendas com upsell
     * @param int $saleId
     * @return array
     */
    public function addItemsToOrder(int $saleId)
    {
        try {
            $this->method = __METHOD__;
            $saleModel = new Sale();

            $firstSale = $saleModel->with(["upsells.productsPlansSale"])->find($saleId);

            $orderData = $this->prepareOrder($firstSale);

            if (empty($orderData)) {
                return [
                    "status" => "error",
                    "message" => "Venda não atende requisitos para gerar ordem no shopify.",
                ];
            }

            foreach ($firstSale->upsells as $sale) {
                $totalValue = $sale->present()->getSubTotal();
                $firstProduct = true;
                foreach ($sale->productsPlansSale as $productsPlanSale) {
                    $productPrice = 0;
                    if ($firstProduct) {
                        if (!empty($sale->shopify_discount)) {
                            $totalValue -= preg_replace("/[^0-9]/", "", $sale->shopify_discount);
                        }
                        if (!empty($sale->automatic_discount)) {
                            $totalValue -= preg_replace("/[^0-9]/", "", $sale->automatic_discount);
                        }
                        if ($productsPlanSale->amount > 1) {
                            $productPrice = intval($totalValue / $productsPlanSale->amount);
                        } else {
                            $productPrice = $totalValue;
                        }
                        $productPrice = substr_replace($productPrice, ".", strlen($productPrice) - 2, 0);
                        $firstProduct = false;
                    }

                    $product = $productsPlanSale->product;

                    $orderData["line_items"][] = [
                        "grams" => 500,
                        "id" => $productsPlanSale->plan_id,
                        "price" => $productPrice,
                        "product_id" => $product->shopify_id,
                        "quantity" => $productsPlanSale->amount,
                        "requires_shipping" => true,
                        "sku" => $productsPlanSale->product->sku,
                        "title" => $product->name,
                        "variant_id" => $product->shopify_variant_id,
                        "variant_title" => $product->description,
                        "name" => $product->name,
                        "gift_card" => false,
                    ];
                }

                $totalValue /= 100;
                $orderPrice = preg_replace("/[^0-9]/", "", $orderData["total_price"]) / 100;
                $orderData["total_price"] = $orderPrice + $totalValue;
                if ($sale->payment_method == 1) {
                    $orderData["transactions"][] = [
                        "gateway" => "nexuspay",
                        "authorization" => hashids_encode($sale->id, "sale_id"),
                        "kind" => "sale",
                        "status" => "success",
                        "amount" => $totalValue,
                    ];
                }
            }

            $this->sendData = $orderData;
            $order = $this->getClient()
                ->getOrderManager()
                ->create($orderData);
            $this->receivedData = $this->convertToArray($order);

            $oldOrderId = $firstSale->shopify_order;

            try {
                $fulfillments = $this->getClient()
                    ->getFulfillmentManager()
                    ->findAll($oldOrderId);
                foreach ($fulfillments as $fulfillment) {
                    $this->getClient()
                        ->getFulfillmentManager()
                        ->cancel($oldOrderId, $fulfillment->getId());
                }
                $this->getClient()
                    ->getOrderManager()
                    ->cancel($oldOrderId);
                $this->getClient()
                    ->getOrderManager()
                    ->remove($oldOrderId);
            } catch (Exception $e) {
            }

            $orderId = $order->getId();

            $firstSale->update([
                "shopify_order" => $orderId,
            ]);

            foreach ($firstSale->upsells as $upsell) {
                if ($upsell->shopify_order != $oldOrderId) {
                    try {
                        $fulfillments = $this->getClient()
                            ->getFulfillmentManager()
                            ->findAll($upsell->shopify_order);
                        foreach ($fulfillments as $fulfillment) {
                            $this->getClient()
                                ->getFulfillmentManager()
                                ->cancel($upsell->shopify_order, $fulfillment->getId());
                        }
                        $this->getClient()
                            ->getOrderManager()
                            ->cancel($upsell->shopify_order);
                        $this->getClient()
                            ->getOrderManager()
                            ->remove($upsell->shopify_order);
                    } catch (Exception $e) {
                    }
                }

                $upsell->shopify_order = $orderId;
                $upsell->save();
            }

            return [
                "status" => "success",
                "message" => "Ordem gerada com sucesso.",
            ];
        } catch (Exception $e) {
            $this->exceptions[] = $e->getMessage();
            report($e);

            return [
                "status" => "success",
                "message" => "Erro ao gerar order",
            ];
        }
    }

    /**
     * @param $sale
     * @return bool
     * @throws Exception
     */
    public function refundOrder($sale)
    {
        try {
            $this->method = __METHOD__;
            $this->saleId = $sale->id;

            $order = $this->client->get("orders/" . $sale->shopify_order);
            if (!FoxUtils::isEmpty($order)) {
                if ($order["order"]["financial_status"] == "pending") {
                    $data = $sale->shopify_order;
                    $this->sendData = $data;
                    $result = $this->client->getOrderManager()->cancel($data);
                    $this->receivedData = $this->convertToArray($result);
                    // caso getOrderManager->cancel da error, trocar por esse( porem esse deleta a ordem, não cancela)
                    //                    $result = $this->client->delete('orders/' . $order['order']['id']);
                    //                    $this->receivedData = $result;
                } else {
                    $transaction = [
                        "gateway" => "nexuspay",
                        "authorization" => hashids_encode($sale->id, "sale_id"),
                        "kind" => "refund",
                        "source" => "external",
                        "amount" => "",
                    ];
                    $this->sendData = $transaction;
                    $result = $this->client->getTransactionManager()->create($sale->shopify_order, $transaction);
                    $this->receivedData = $this->convertToArray($result);
                }
            } else {
                return false;
            }
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
            if (method_exists($ex, "getCode") && in_array($ex->getCode(), [401, 402, 403, 404, 423, 429])) {
                return [];
            }
            throw $ex;
        }
    }

    /**
     * @param $object
     * @return array
     * @author Fausto Marins
     */
    public function convertToArray($object)
    {
        try {
            $result = [];
            foreach ((object) (array) $object as $key => $value) {
                if (is_string($value) || is_null($value)) {
                    $result[$key] = $value;
                } else {
                    if (is_array($value)) {
                        $sub = [];
                        foreach ($value as $arrayKey => $arrayValue) {
                            foreach ((object) (array) $arrayValue as $k => $v) {
                                $sub[$arrayKey][$k] = $v;
                            }
                        }
                        $result[$key] = $sub;
                    } else {
                        $sub = [];
                        foreach ((object) (array) $value as $k => $v) {
                            $sub[$k] = $v;
                        }
                        $result[$key] = $sub;
                    }
                }
            }

            return $result;
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
            report($ex);

            return [];
        }
    }

    /**
     * @return int
     */
    private function getSaleId()
    {
        return $this->saleId;
    }

    /**
     * @return array|false|string
     */
    private function getSendData()
    {
        return json_encode($this->sendData ?? []);
    }

    /**
     * @return array|false|string
     */
    private function getReceivedData()
    {
        return json_encode($this->receivedData ?? []);
    }

    /**
     * @return false|string|null
     */
    private function getExceptions()
    {
        $exceptions = $this->exceptions ?? [];
        if (FoxUtils::isEmpty($exceptions)) {
            return null;
        } else {
            return json_encode($exceptions);
        }
    }

    /**
     * @return string
     */
    private function getProject()
    {
        return $this->project;
    }

    /**
     * @return string
     */
    private function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    private function getAllData()
    {
        return [
            "project" => $this->getProject(),
            "method" => $this->getMethod(),
            "sale_id" => $this->getSaleId(),
            "send_data" => $this->getSendData(),
            "received_data" => $this->getReceivedData(),
            "exceptions" => $this->getExceptions(),
        ];
    }

    /**
     * @return void
     */
    public function saveSaleShopifyRequest()
    {
        try {
            SaleShopifyRequest::create($this->getAllData());

            return;
        } catch (Exception $ex) {
            report($ex);

            return;
        }
    }

    /**
     * @return string[]
     * Ensure if the token entered at integration
     * creation has the required permissions
     */
    public function verifyPermissions()
    {
        $permissions = $this->testOrdersPermissions();

        if ($permissions["status"] == "error") {
            return $permissions;
        }

        $permissions = $this->testProductsPermissions();

        if ($permissions["status"] == "error") {
            return $permissions;
        }
        $permissions = $this->testThemePermissions();

        if ($permissions["status"] == "error") {
            return $permissions;
        }

        return $permissions;
    }

    /**
     * @return string[]
     * Verify if the informed token has permission to manage orders on shopify
     */
    public function testOrdersPermissions()
    {
        try {
            $items = [];

            $items[] = [
                "grams" => 500,
                "id" => 100,
                "price" => 100.0,
                "product_id" => 1000,
                "quantity" => 1,
                "requires_shipping" => true,
                "sku" => 1234566789,
                "title" => "Nexus Pay Test",
                "variant_id" => 20000,
                "variant_title" => "Nexus Pay Test",
                "name" => "Nexus Pay Test",
                "gift_card" => false,
            ];

            $shippingAddress = [
                "address1" => "Rio Grande do Sul - RS",
                "address2" => "",
                "city" => "Porto Alegre",
                "company" => "25800004021",
                "country" => "Brasil",
                "first_name" => "Cloud",
                "last_name" => "Fox",
                "phone" => "+5524999999999",
                "province" => "RS",
                "zip" => "",
                "name" => "Nexus Pay",
                "country_code" => "BR",
                "province_code" => "",
            ];

            $orderData = [
                "accepts_marketing" => false,
                "currency" => "BRL",
                "email" => "test@nexuspay.com.br",
                "phone" => "+5524999999999",
                "first_name" => "Cloud",
                "last_name" => "Fox",
                "buyer_accepts_marketing" => false,
                "line_items" => $items,
                "shipping_address" => $shippingAddress,
            ];

            $orderData += [
                "transactions" => [
                    [
                        "gateway" => "nexuspay",
                        "authorization" => "PERMISSIONS_TEST",
                        "kind" => "sale",
                        "status" => "success",
                        "amount" => 100.0,
                    ],
                ],
            ];

            // $order = $this->client->getOrderManager()->create($orderData);
            $order = $this->client->post("orders", [
                "order" => $orderData,
            ]);

            // dd($order);

            // if (empty($order) || empty($order->getId())) {
            if (empty($order) || empty($order["order"]["id"])) {
                return [
                    "status" => "error",
                    "message" => "Erro na permissão de pedidos",
                ];
            }

            // $this->client->getOrderManager()->remove($order->getId());
            $this->client->delete("orders/" . $order["order"]["id"]);

            return [
                "status" => "success",
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => "Erro na permissão de pedidos",
            ];
        }
    }

    /**
     * @return string[]
     * Verify if the informed token has permission to manage products on shopify
     */
    public function testProductsPermissions()
    {
        try {
            $products = $this->client->getProductManager()->findAll();

            if (empty($products)) {
                return [
                    "status" => "error",
                    "message" => "Erro na permissão de produtos",
                ];
            }

            foreach ($products as $product) {
                foreach ($product->getVariants() as $variant) {
                    if (!empty($this->getShopInventoryItem($variant->getInventoryItemId()))) {
                        $productCost = $this->getShopInventoryItem($variant->getInventoryItemId())->getCost();
                        break;
                    }
                }

                return [
                    "status" => "success",
                ];
            }
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => "Erro na permissão de produtos",
            ];
        }
    }

    /**
     * @return string[]
     * Verify if the informed token has permission to edit theme assets on shopify
     */
    public function testThemePermissions()
    {
        try {
            $this->templateService->setThemeByRole("main");

            if (empty($this->templateService->getTheme())) {
                return [
                    "status" => "error",
                    "message" => "Erro na permissão de tema",
                ];
            }

            $this->client->getAssetManager()->update($this->templateService->getTheme()->getId(), [
                "key" => $this->templateService::LAYOUT_THEME_LIQUID,
                "value" => $this->templateService->getTemplateHtml(),
            ]);

            return [
                "status" => "success",
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => "Erro na permissão de tema",
            ];
        }
    }

    public function updateOrder(Sale $sale)
    {
        try {
            $this->method = __METHOD__;
            $this->saleId = $sale->id;
            if (!empty($sale) && !empty($sale->shopify_order)) {
                $client = $sale->customer;

                $shippingAddress = [
                    "phone" => $client->telephone,
                ];

                $orderData = [
                    "email" => $client->email,
                    "phone" => $client->telephone,
                    "shipping_address" => $shippingAddress,
                ];

                $this->sendData = $orderData;
                $order = $this->client->put("orders/" . $sale->shopify_order, [
                    "order" => $orderData,
                ]);

                $this->receivedData = $order;
            } else {
                Log::emergency("Erro ao atualizar uma ordem no shopify com a venda " . $sale->id);
            }
        } catch (Exception $e) {
            $this->exceptions[] = $e->getMessage();
            Log::emergency("Erro ao atualizar uma ordem no shopify com a venda " . $sale->id);
        }
    }

    public function findFulfillments($orderId)
    {
        try {
            return $this->client->getFulfillmentManager()->findAll($orderId);
        } catch (Exception $e) {
            $this->exceptions[] = $e->getMessage();
            (new ShopifyErrors())->FormatDataInvalidShopifyIntegration($e);

            return null;
        }
    }

    /**
     * @param $sale
     */
    public function cancelOrder($sale)
    {
        try {
            $this->method = __METHOD__;
            $this->saleId = $sale->id;
            $url = "orders/" . $sale->shopify_order . "/cancel";
            $data = [
                "reason" => "fraud",
            ];

            $this->sendData = $data;
            $result = $this->shopifyClient->post($url, $data);
            $this->receivedData = json_encode($result);
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
        }
    }

    public function getCostShopify($variant)
    {
        try {
            $cost = $this->getShopInventoryItem($variant->getInventoryItemId());
            if (!is_array($cost) && method_exists($cost, "getCost")) {
                $cost = $cost->getCost();
                $cost = empty($cost) ? 0 : $cost;
            } else {
                $cost = "";
            }
            return $cost;
        } catch (Exception $e) {
            return "";
        }
    }
}
