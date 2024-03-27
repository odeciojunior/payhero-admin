<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleShopifyRequest;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\User;
use Modules\Core\Events\ShopifyIntegrationReadyEvent;
use Modules\Core\Services\Shopify\AssetService;
use Modules\Core\Services\Shopify\Client;
use Modules\Core\Services\Shopify\FulfillmentService;
use Modules\Core\Services\Shopify\InventoryService;
use Modules\Core\Services\Shopify\OrderService;
use Modules\Core\Services\Shopify\ProductImageService;
use Modules\Core\Services\Shopify\ProductService;
use Modules\Core\Services\Shopify\ShopService;
use Modules\Core\Services\Shopify\TransactionService;
use Modules\Core\Services\Shopify\WebhookService;

class ShopifyService
{
    public ShopifyTemplateService $templateService;

    private $saleId;

    private $sendData = [];

    private $receivedData = [];

    private $exceptions = [];

    private $method;

    private OrderService $orderService;

    private FulfillmentService $fulfillmentService;

    private TransactionService $transactionService;

    private ShopService $shopService;

    private ProductService $productService;

    private InventoryService $inventoryService;

    private WebhookService $webhookService;

    private ProductImageService $productImageService;

    private AssetService $assetService;

    private $project = "admin";

    public function __construct(string $urlStore, string $token)
    {
        $this->templateService = new ShopifyTemplateService($urlStore, $token);

        $client = new Client($urlStore, $token);
        $this->orderService = new OrderService($client);
        $this->fulfillmentService = new FulfillmentService($client);
        $this->transactionService = new TransactionService($client);
        $this->shopService = new ShopService($client);
        $this->productService = new ProductService($client);
        $this->inventoryService = new InventoryService($client);
        $this->webhookService = new WebhookService($client);
        $this->productImageService = new ProductImageService($client);
        $this->assetService = new AssetService($client);
    }

    public function importShopifyProduct($projectId, $userId, $shopifyProductId): bool
    {
        $storeProduct = $this->getShopProduct($shopifyProductId);
        //sleep(1);

        if (empty($storeProduct)) {
            return false;
        }

        $products = Product::with("productsPlans.plan")
            ->where("project_id", $projectId)
            ->get();

        $statusProductShopify = 1;

        $productsArray = [];
        foreach ($storeProduct->variants as $variant) {
            $title = "";

            try {
                $description = $variant->option1;
                if ($description == "Default Title") {
                    $description = "";
                }
                if ($variant->option2 != "") {
                    $description .= " - " . $variant->option2;
                }
                if ($variant->option3 != "") {
                    $description .= " - " . $variant->option3;
                }
                if (empty($storeProduct->title)) {
                    $title = "Produto sem nome";
                } else {
                    $title = mb_substr($storeProduct->title, 0, 100);
                }
            } catch (Exception $e) {
            }

            $product = $products
                ->where("shopify_id", $storeProduct->id)
                ->where("shopify_variant_id", $variant->id)
                ->where("project_id", $projectId)
                ->first();

            if ($product) {
                $productsArray[] = $product->id;
                $product->fill([
                    "name" => $title,
                    "description" => mb_substr($description, 0, 100),
                    "weight" => $variant->weight,
                    "shopify_id" => $storeProduct->id,
                    "shopify_variant_id" => $variant->id,
                    "sku" => $variant->sku,
                    "project_id" => $projectId,
                    "active_flag" => $statusProductShopify,
                ]);

                $productPlan = $product->productsPlans
                    ->where("amount", 1)
                    ->sortBy("id")
                    ->first();
                if (!empty($productPlan)) {
                    $plan = $productPlan->plan;
                    $plan->fill([
                        "name" => $title,
                        "description" => mb_substr($description, 0, 100),
                        "price" => $variant->price,
                        "status" => "1",
                        "active_flag" => $statusProductShopify,
                        "project_id" => $projectId,
                        "name" => $title,
                        "description" => mb_substr($description, 0, 100),
                        "price" => $variant->price,
                        "status" => "1",
                        "active_flag" => $statusProductShopify,
                        "project_id" => $projectId,
                    ]);
                    if ($plan->isDirty()) {
                        $plan->save();
                    }

                    $photo = "";
                    if (count($storeProduct->variants) > 1) {
                        foreach ($storeProduct->images as $image) {
                            $variantIds = $image->variant_ids;
                            foreach ($variantIds as $variantId) {
                                if ($variantId == $variant->id) {
                                    if ($image->src != "") {
                                        $photo = $image->src;
                                    } else {
                                        $photo = $storeProduct->image->src;
                                    }
                                }
                            }
                        }
                    }
                    if (empty($photo)) {
                        $image = $storeProduct->image;
                        if (!empty($image)) {
                            try {
                                $photo = $image->src;
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
                        "shopify_id" => $storeProduct->id,
                        "shopify_variant_id" => $variant->id,
                        "project_id" => $projectId,
                        "name" => $title,
                        "description" => mb_substr($description, 0, 100),
                        "code" => "",
                        "price" => $variant->price > 100000 ? 100 : $variant->price,
                        "status" => "1",
                        "active_flag" => $statusProductShopify,
                    ]);

                    $dataProductPlan = [
                        "product_id" => $product->id,
                        "plan_id" => $plan->id,
                        "amount" => 1,
                    ];

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
                    "shopify" => true,
                    "price" => "",
                    "shopify_id" => $storeProduct->id,
                    "shopify_variant_id" => $variant->id,
                    "sku" => $variant->sku,
                    "project_id" => $projectId,
                    "active_flag" => $statusProductShopify,
                ]);

                $productsArray[] = $product->id;
                $plan = Plan::create([
                    "shopify_id" => $storeProduct->id,
                    "shopify_variant_id" => $variant->id,
                    "project_id" => $projectId,
                    "name" => $title,
                    "description" => mb_substr($description, 0, 100),
                    "code" => "",
                    "price" => $variant->price > 100000 ? 100 : $variant->price,
                    "status" => "1",
                    "active_flag" => $statusProductShopify,
                ]);
                $plan->update(["code" => hashids_encode($plan->id)]);

                $dataProductPlan = [
                    "product_id" => $product->id,
                    "plan_id" => $plan->id,
                    "amount" => "1",
                ];

                ProductPlan::create($dataProductPlan);

                $photo = "";
                if (count($storeProduct->variants) > 1) {
                    foreach ($storeProduct->images as $image) {
                        $variantIds = $image->variant_ids;
                        foreach ($variantIds as $variantId) {
                            if ($variantId == $variant->id) {
                                if ($image->src != "") {
                                    $photo = $image->src;
                                } else {
                                    $photo = $storeProduct->image->src;
                                }
                            }
                        }
                    }
                }
                if (empty($photo)) {
                    $image = $storeProduct->image;
                    if (!empty($image)) {
                        try {
                            $photo = $image->src;
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
    }

    public function importShopifyStore($projectId, $userId)
    {
        ShopifyIntegration::where("project_id", $projectId)->update([
            "status" => ShopifyIntegration::STATUS_PENDING,
        ]);

        $project = Project::find($projectId);

        $pagination = $this->getShopProducts();
        
        $storeProducts = $pagination->current();

        $nextPagination = true;

        while ($nextPagination) {
            foreach ($storeProducts as $shopifyProduct) {
                try {
                    $this->importShopifyProduct($projectId, $userId, $shopifyProduct->id);
                } catch (Exception $e) {
                    report($e);
                }
            }

            if ($pagination->hasNext()) {
                $nextPageInfo = $pagination->getNextPageInfo();
                sleep(1);
                $storeProducts = $this->getShopProducts($nextPageInfo);
            } else {
                $nextPagination = false;
            }
        }

        if (FoxUtils::isProduction()) {
            $this->createShopifyIntegrationWebhook($projectId, "https://admin.azcend.com.br/postback/shopify/");
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

        sleep(1);
        $this->createShopWebhook([
            "topic" => "products/update",
            "address" => $postbackUrl . hashids_encode($projectId),
            "format" => "json",
        ]);

        return true;
    }

    public function getShopName()
    {
        $config = $this->shopService->show();
        return $config->name;
    }

    public function getShopDomain()
    {
        $config = $this->shopService->show();
        return $config->domain;
    }

    public function getShopId()
    {
        $config = $this->shopService->show();
        return $config->id;
    }

    public function getShopProducts($pageInfo = null)
    {
        $queryParams = ["limit" => 250];
        if (!empty($pageInfo)) {
            $queryParams["page_info"] = $pageInfo;
        }
        return $this->productService->findAll($queryParams);
    }

    public function getShopProduct($productId)
    {
        try {
            return $this->productService->find($productId);
        } catch (Exception $e) {
            return null;
        }
    }

    public function createShopWebhook($data = [])
    {
        return $this->webhookService->create(["webhook" => $data]);
    }

    public function getShopWebhook($webhookId = null)
    {
        try {
            if (!empty($webhookId)) {
                return $this->webhookService->find($webhookId);
            }

            return $this->webhookService->findAll();
        } catch (Exception $e) {
            report($e);
            return [];
        }
    }

    public function deleteShopWebhook($webhookId = null)
    {
        try {
            if (!empty($webhookId)) {
                return $this->webhookService->delete($webhookId);
            }

            $webhooks = $this->getShopWebhook();
            foreach ($webhooks as $webhook) {
                $this->webhookService->delete($webhook->id);
                sleep(1);
            }

            return [];
        } catch (Exception $e) {
            throw $e;
        }
    }

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
                "token_azcend" => hashids_encode($sale->checkout_id),
            ],
            "total_price" => substr_replace($totalValue, ".", strlen($totalValue) - 2, 0),
        ];

        if ($sale->payment_method == Sale::CREDIT_CARD_PAYMENT) {
            $orderData += [
                "transactions" => [
                    [
                        "gateway" => "azcend",
                        "authorization" => hashids_encode($sale->id, "sale_id"),
                        "kind" => "sale",
                        "status" => "success",
                        "amount" => foxutils()->floatFormat($totalValue),
                    ],
                ],
            ];
        } else {
            if ($sale->payment_method == Sale::BILLET_PAYMENT || $sale->payment_method == Sale::PIX_PAYMENT) {
                $orderData += [
                    "financial_status" => $sale->status == 1 ? "paid" : "pending",
                    "transactions" => [
                        [
                            "gateway" => "azcend",
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

            $order = $this->orderService->create([
                "order" => $orderData,
            ]);

            $this->receivedData = $order;

            if (FoxUtils::isEmpty($order->id)) {
                return [
                    "status" => "error",
                    "message" => "Error ao tentar gerar ordem no shopify.",
                ];
            }
            $sale->update([
                "shopify_order" => $order->id,
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

            $order = $this->orderService->create([
                "order" => $orderData,
            ]);

            $this->receivedData = $order;

            if (FoxUtils::isEmpty($order->id)) {
                return [
                    "status" => "error",
                    "message" => "Error ao tentar gerar ordem no shopify.",
                ];
            }
            $sale->update([
                "shopify_order" => $order->id,
            ]);

            return [
                "status" => "success",
                "message" => "Ordem gerada com sucesso.",
            ];
        }
    }

    /**
     * Usado para gerar order de vendas com upsell
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
                        "gateway" => "azcend",
                        "authorization" => hashids_encode($sale->id, "sale_id"),
                        "kind" => "sale",
                        "status" => "success",
                        "amount" => $totalValue,
                    ];
                }
            }

            $this->sendData = $orderData;
            $order = $this->orderService->create(["order" => $orderData]);
            $this->receivedData = $this->convertToArray($order);

            $oldOrderId = $firstSale->shopify_order;

            try {
                $fulfillments = $this->fulfillmentService->findAll($oldOrderId);
                foreach ($fulfillments as $fulfillment) {
                    $this->fulfillmentService->cancel($fulfillment->id);
                }
                $this->orderService->cancel($oldOrderId);
                $this->orderService->delete($oldOrderId);
            } catch (Exception $e) {
            }

            $orderId = $order->id;

            $firstSale->update([
                "shopify_order" => $orderId,
            ]);

            foreach ($firstSale->upsells as $upsell) {
                if (!empty($upsell->shopify_order) && $upsell->shopify_order != $oldOrderId) {
                    try {
                        $fulfillments = $this->fulfillmentService->findAll($upsell->shopify_order);
                        foreach ($fulfillments as $fulfillment) {
                            $this->fulfillmentService->cancel($fulfillment->id);
                        }
                        $this->orderService->cancel($upsell->shopify_order);
                        $this->orderService->delete($upsell->shopify_order);
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
     * @throws Exception
     */
    public function refundOrder($sale)
    {
        try {
            $this->method = __METHOD__;
            $this->saleId = $sale->id;

            $order = $this->orderService->find($sale->shopify_order);
            if (!FoxUtils::isEmpty($order)) {
                if ($order->financial_status == "pending") {
                    $data = $sale->shopify_order;
                    $this->sendData = $data;
                    $result = $this->orderService->cancel($data);
                    $this->receivedData = $this->convertToArray($result);
                } else {
                    $transaction = [
                        "gateway" => "azcend",
                        "authorization" => hashids_encode($sale->id, "sale_id"),
                        "kind" => "refund",
                        "source" => "external",
                        "amount" => "",
                    ];
                    $this->sendData = $transaction;
                    $result = $this->transactionService->create($sale->shopify_order, [
                        "transaction" => $transaction,
                    ]);
                    $this->receivedData = $this->convertToArray($result);
                }
            } else {
                return false;
            }
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
            throw $ex;
        }
    }

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

    private function getSaleId()
    {
        return $this->saleId;
    }

    private function getSendData()
    {
        return json_encode($this->sendData ?? []);
    }

    private function getReceivedData()
    {
        return json_encode($this->receivedData ?? []);
    }

    private function getExceptions()
    {
        $exceptions = $this->exceptions ?? [];
        if (FoxUtils::isEmpty($exceptions)) {
            return null;
        } else {
            return json_encode($exceptions);
        }
    }

    private function getProject()
    {
        return $this->project;
    }

    private function getMethod()
    {
        return $this->method;
    }

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
                "title" => "Azcend Test",
                "variant_id" => 20000,
                "variant_title" => "Azcend Test",
                "name" => "Azcend Test",
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
                "name" => "Azcend",
                "country_code" => "BR",
                "province_code" => "",
            ];

            $orderData = [
                "accepts_marketing" => false,
                "currency" => "BRL",
                "email" => "test@azcend.com.br",
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
                        "gateway" => "azcend",
                        "authorization" => "PERMISSIONS_TEST",
                        "kind" => "sale",
                        "status" => "success",
                        "amount" => 100.0,
                    ],
                ],
            ];

            $order = $this->orderService->create([
                "order" => $orderData,
            ]);

            if (empty($order) || empty($order->id)) {
                return [
                    "status" => "error",
                    "message" => "Erro na permissão de pedidos",
                ];
            }

            $this->orderService->delete($order->id);

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

    public function testProductsPermissions()
    {
        try {
            $products = $this->productService->findAll()->current();

            if (empty($products)) {
                return [
                    "status" => "error",
                    "message" => "Erro na permissão de produtos",
                ];
            }

            foreach ($products as $product) {
                foreach ($product->variants as $variant) {
                    try {
                        $this->inventoryService->find($variant->inventory_item_id);
                    } catch (Exception $e) {
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

            $this->assetService->createOrUpdateAsset(
                $this->templateService->getTheme()->id,
                $this->templateService::LAYOUT_THEME_LIQUID,
                $this->templateService->getTemplateHtml(),
            );

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

    public function findFulfillments($orderId)
    {
        try {
            return $this->fulfillmentService->findAll($orderId);
        } catch (Exception $e) {
            $this->exceptions[] = $e->getMessage();
            (new ShopifyErrors())->FormatDataInvalidShopifyIntegration($e);

            return null;
        }
    }
}
