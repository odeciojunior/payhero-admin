<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SaleService;
use Slince\Shopify\Client;
use Modules\Core\Entities\Plan;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\ProductPlan;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Events\SaleApprovedEvent;
use Slince\Shopify\Client as ShopifyClient;
use Modules\Core\Entities\ShopifyIntegration;

/**
 * Class SetApprovedShopifyOrderListener
 * @package Modules\Core\Listeners
 */
class SetApprovedShopifyOrderListener implements ShouldQueue
{
    use Queueable;

    /**
     * @param SaleApprovedEvent $event
     */
    public function handle(SaleApprovedEvent $event)
    {
        $shopifyIntegrationModel = new ShopifyIntegration();
        $planSaleModel           = new PlanSale();
        $planModel               = new Plan();
        $productPlanModel        = new ProductPlan();
        $saleService             = new SaleService();

        if ($event->sale->payment_method == 1) {
            $shopifyIntegration = $shopifyIntegrationModel->where('project', $event->project->id)->first();

            if (!empty($shopifyIntegration)) {
                $credential    = new PublicAppCredential($shopifyIntegration->token);
                $shopifyClient = new ShopifyClient($credential, $shopifyIntegration->url_store, [
                    'metaCacheDir' => './tmp',
                ]);

                $names     = explode(" ", $event->delivery->receiver_name);
                $telephone = str_replace("+", '', $event->client->telephone);
                if (strlen($telephone) != 14) {
                    $telephone = "+557734881234";
                }

                $plansSale = $planSaleModel->where('sale', $event->sale->id)->get();

                $plans = [];
                foreach ($plansSale as $planSale) {

                    $plan    = $planModel->find($planSale->plan);
                    $plans[] = [
                        "id"           => $plan->id,
                        'name'         => $plan->name,
                        "price"        => $plan->price,
                        "product_name" => $plan->name,
                        'quantity'     => $planSale->amount,
                    ];
                }

                $products = $saleService->getProducts($event->sale->id);;

                if (!empty($event->sale->shopify_discount)) {
                    $plans[0]['price'] = preg_replace("/[^0-9]/", "", $plans[0]['price']) - (intval(preg_replace("/[^0-9]/", "", $event->sale->shopify_discount) / $products[0]['amount']));
                    $plans[0]['price'] = $plans[0]['price'] / 100;
                    substr_replace($plans[0]['price'], '.', strlen($plans[0]['price']) - 2, 0);
                }

                $totalValue = 0;
                foreach ($plans as $plan) {
                    $totalValue += preg_replace("/[^0-9]/", "", $plan['price']);
                }

                $totalValue -= preg_replace("/[^0-9]/", "", $event->sale->shipment_value);

                $firstProduct = true;
                $items        = [];
                foreach ($plans as $key => $plan) {

                    foreach ($products as $product) {

                        $productPrice = 0;
                        if ($firstProduct) {
                            if ($product['amount'] > 1) {
                                $productAmount = $productPlanModel->where('product', $product['id'])
                                                                  ->first()->amount;
                                $productPrice  = intval($totalValue / $productAmount);
                            } else {
                                $productPrice = $totalValue;
                            }
                            $productPrice = substr_replace($productPrice, '.', strlen($productPrice) - 2, 0);
                            $firstProduct = false;
                        }
                        $productAmounts = $productPlanModel->where('product', $product['id'])
                                                           ->first()->amount;

                        $items[] = [
                            "grams"             => 500,
                            "id"                => $plan['id'],
                            "price"             => $productPrice,
                            "product_id"        => $product['shopify_id'],
                            "quantity"          => $plan['quantity'] * $productAmounts,
                            "requires_shipping" => true,
                            "sku"               => $plan['name'],
                            "title"             => $plan['name'],
                            "variant_id"        => $product['shopify_variant_id'],
                            "variant_title"     => $plan['name'],
                            "name"              => $plan['name'],
                            "gift_card"         => false,
                        ];
                    }
                }

                $address = $event->delivery->street . ' - ' . $event->delivery->number;
                if ($event->delivery->complement != '') {
                    $address .= ' - ' . $event->delivery->complement;
                }
                $address .= ' - ' . $event->delivery->neighborhood;

                $shippingAddress = [
                    "address1"      => $address,
                    "address2"      => "",
                    "city"          => $event->delivery->city,
                    "company"       => $event->client->document,
                    "country"       => "Brasil",
                    "first_name"    => $names[0],
                    "last_name"     => $names[count($names) - 1],
                    "phone"         => $telephone,
                    "province"      => $event->delivery->state,
                    "zip"           => FoxUtils::formatCEP($event->delivery->zip_code),
                    "name"          => $event->client->name,
                    "country_code"  => "BR",
                    "province_code" => $event->delivery->state,
                ];

                $order = $shopifyClient->getOrderManager()->create([
                                                                       "accepts_marketing"       => false,
                                                                       "currency"                => "BRL",
                                                                       "email"                   => $event->client->email,
                                                                       "first_name"              => $names[0],
                                                                       "last_name"               => $names[count($names) - 1],
                                                                       "buyer_accepts_marketing" => false,
                                                                       "line_items"              => $items,
                                                                       "shipping_address"        => $shippingAddress,
                                                                   ]);

                $event->sale->update([
                                         'shopify_order' => $order->getId(),
                                     ]);
            }
        } else {
            $shopifyIntegration = $shopifyIntegrationModel->where('project', $event->plan->project)->first();

            if (!empty($shopifyIntegration)) {

                try {
                    $credential = new PublicAppCredential($shopifyIntegration['token']);

                    $client = new Client($credential, $shopifyIntegration['url_store'], [
                        'metaCacheDir' => './tmp',
                    ]);

                    $client->getTransactionManager()->create($event->sale->shopify_order, [
                        "kind" => "capture",
                    ]);
                } catch (Exception $e) {
                    Log::warning('erro ao alterar estado do pedido no shopify com a venda ' . $event->sale->id);
                    report($e);
                }
            }
        }
    }
}
