<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Sale;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ReportanaSent;
use Modules\Core\Entities\ReportanaIntegration;

/**
 * Class ReportanaService
 * @package Modules\Core\Services
 */
class ReportanaService
{
    /**
     * @var
     */
    public $urlApi;
    /**
     * @var
     */
    private $integrationId;

    /**
     * ReportanaService constructor.
     * @param $urlApi
     * @param $integrationId
     */
    function __construct($urlApi, $integrationId)
    {
        $this->urlApi = $urlApi;
        $this->integrationId = $integrationId;
    }

    /**
     * @param $data
     * @param $url
     * @return array
     */
    private function sendPost($data)
    {
        $data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $headers = ["Content-Type: application/json", "Content-Length: " . strlen($data)];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            return ["code" => $httpCode, "result" => curl_error($ch)];
        }
        curl_close($ch);

        return ["code" => $httpCode, "result" => json_decode($result, true)];
    }

    private function sendPostApi($data)
    {
        if (!foxutils()->isProduction()) {
            return [
                "code" => 403,
                "result" => "Funcionalidade habilitada somente em ambiente de produção!"
            ];
        }

        $data = json_encode($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->urlApi);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $headers = [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode(env("REPORTANA_CLIENTE_ID") . ":" . env("REPORTANA_CLIENTE_SECRET"))
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch);

        if (curl_errno($ch)) {
            return [
                "code" => $httpCode,
                "result" => curl_error($ch)
            ];
        }

        curl_close($ch);

        return [
            "code" => $httpCode,
            "result" => json_decode($result, true)
        ];
    }

    public function sendAbandonedCartApi($checkout, $plansCheckout, $domain, $log, $trackingCreatedEvent = false)
    {
        try {
            $dataProducts = [];
            $total = 0;
            foreach ($plansCheckout as $planCheckout) {
                $dataProducts[] = [
                    "id" => $planCheckout->plan_id,
                    "title" => $planCheckout->plan->name,
                    "quantity" => $planCheckout->amount,
                    "price" => $planCheckout->plan->price,
                    "image_url" => $planCheckout->plan->products->first()->photo ?? "",
                ];

                $total += $planCheckout->plan->price * $planCheckout->amount;
            }

            if (empty(preg_replace("/[^0-9]/", "", $log->total_value))) {
                $totalValue = $total;
            } else {
                $totalValue = number_format(
                    preg_replace("/[,]/", ".", preg_replace("/[^0-9,]/", "", $log->total_value)),
                    2
                );
            }

            $domainName = $domain->name ?? "nexuspay.vip";

            $checkoutLink = "https://checkout." . $domainName . "/recovery/" . $checkout->id_code . "?recovery=true";

            $firstName = $log->name ? explode(" ", $log->name)[0] : "";
            $lastName = $log->name ? explode(" ", $log->name)[count(explode(" ", $log->name)) - 1] : "";

            $data = [
                "reference_id" => $checkout->id_code,
                "number" => strval($checkout->id),
                "customer_name" => $log->name ?? "",
                "customer_email" => $log->email ?? "",
                "customer_phone" => $log->telephone ?? "",
                "billing_address" => [
                    "name" => $log->name ?? "",
                    "first_name" => $firstName,
                    "last_name" => $lastName,
                    "company" => null,
                    "phone" => $log->telephone ?? "",
                    "address1" => $log->street ?? "",
                    "address2" => null,
                    "city" => $log->city ?? "",
                    "province" => $log->neighborhood ?? "",
                    "province_code" => $log->state ?? "",
                    "country" => "Brazil",
                    "country_code" => "BR",
                    "zip" => $log->zipcode ?? "",
                    "latitude" => null,
                    "longitude" => null
                ],
                "shipping_address" => [
                    "name" => $log->name ?? "",
                    "first_name" => $firstName,
                    "last_name" => $lastName,
                    "company" => null,
                    "phone" => $log->telephone ?? "",
                    "address1" => $log->street ?? "",
                    "address2" => null,
                    "city" => $log->city ?? "",
                    "province" => $log->neighborhood ?? "",
                    "province_code" => $log->state ?? "",
                    "country" => "Brazil",
                    "country_code" => "BR",
                    "zip" => $log->zipcode ?? "",
                    "latitude" => null,
                    "longitude" => null
                ],
                "line_items" => $dataProducts,
                "currency" => "BRL",
                "total_price" => $totalValue,
                "subtotal_price" => $totalValue,
                "referring_site" => "https://" . $domainName,
                "checkout_url" => $checkoutLink,
                "original_created_at" => $checkout->created_at->format("Y-m-d H:i:s")
            ];

            $return = $this->sendPostApi($data);

            if (isset($return["result"]["success"]) && $return["result"]["success"] == true) {
                $sentStatus = 2;
            } else {
                $sentStatus = 1;
            }

            ReportanaSent::create([
                "data" => json_encode($data),
                "response" => json_encode($return),
                "sent_status" => $sentStatus,
                "instance_id" => $checkout->id,
                "instance" => "checkout",
                "event_sale" => (new ReportanaIntegration())->present()->getEvent("abandoned_cart"),
                "reportana_integration_id" => $this->integrationId,
            ]);

            return $return;
        } catch (Exception $e) {
            Log::warning("Erro ao enviar notificação para Reportana no carrinho abandonado " . $checkout->id);

            report($e);
        }
    }

    public function sendSaleApi($sale, $planSales, $domain, $eventSale, $trackingCreatedEvent = false)
    {
        try {
            $dataProducts = [];
            $total = 0;
            foreach ($planSales as $planSale) {
                $dataProducts[] = [
                    "id" => $planSale->plan_id,
                    "title" => $planSale->plan->name,
                    "quantity" => $planSale->amount,
                    "price" => $planSale->plan->price,
                    "image_url" => $planSale->plan->products->first()->photo ?? "",
                ];

                $total += $planSale->plan->price * $planSale->amount;
            }

            $trackingCodes = $sale->trackings->pluck("tracking_code")->values()->toArray();

            $status = "";
            $paymentMethod = "CREDIT_CARD";
            switch ($eventSale) {
                case "credit_card_refused":
                    $status = "NOT_PAID";
                    $paymentMethod = "CREDIT_CARD";

                    break;
                case "credit_card_paid":
                    $status = "PAID";
                    $paymentMethod = "CREDIT_CARD";

                    break;
                case "abandoned_cart":
                    $status = "NOT_PAID";
                    $paymentMethod = "OTHER";

                    break;
                case "billet_paid":
                    $status = "PAID";
                    $paymentMethod = "BOLETO";

                    break;
                case "billet_pending":
                    $status = "PENDING";
                    $paymentMethod = "BOLETO";

                    break;
                case "billet_expired":
                    $status = "NOT_PAID";
                    $paymentMethod = "BOLETO";

                    break;
                case "pix_paid":
                    $status = "PAID";
                    $paymentMethod = "PIX";

                    break;
                case "pix_pending":
                    $status = "PENDING";
                    $paymentMethod = "PIX";

                    break;
                case "pix_expired":
                    $status = "NOT_PAID";
                    $paymentMethod = "PIX";

                    break;
            }

            $totalValue = number_format(($sale->original_total_paid_value - $sale->interest_total_value) / 100, 2, ".", "");

            $subtotal = number_format($sale->sub_total, 2, ".", "");

            $domainName = $domain->name ?? "nexuspay.vip";

            $checkoutLink = "https://checkout.{$domainName}/recovery/" . $sale->checkout->id_code . "?recovery=true";

            $boletoLink = "https://checkout.{$domainName}/order/" . hashids_encode($sale->id, "sale_id") . "/download-boleto";

            if ($sale->status !== Sale::STATUS_APPROVED) {
                $boletoLink = "https://checkout.{$domainName}/recovery/" . $sale->checkout->id_code . "?recovery=true";
            }

            $firstName = $sale->customer->name ? explode(" ", $sale->customer->name)[0] : "";
            $lastName = $sale->customer->name ? explode(" ", $sale->customer->name)[count(explode(" ", $sale->customer->name)) - 1] : "";

            $data = [
                "reference_id" => hashids_encode($sale->id, "sale_id"),
                "number" => strval($sale->id),
                "customer_name" => $sale->customer->name ?? "",
                "customer_email" => $sale->customer->email ?? "",
                "customer_phone" => $sale->customer->telephone ?? "",
                "billing_address" => [
                    "name" => $sale->customer->name ?? "",
                    "first_name" => $firstName,
                    "last_name" => $lastName,
                    "company" => null,
                    "phone" => $sale->customer->telephone ?? "",
                    "address1" => $sale->delivery->street ?? "",
                    "address2" => null,
                    "city" => $sale->delivery->city ?? "",
                    "province" => $sale->delivery->neighborhood ?? "",
                    "province_code" => $sale->delivery->state ?? "",
                    "country" => "Brazil",
                    "country_code" => "BR",
                    "zip" => $sale->delivery->zipcode ?? "",
                    "latitude" => null,
                    "longitude" => null
                ],
                "shipping_address" => [
                    "name" => $sale->customer->name ?? "",
                    "first_name" => $firstName,
                    "last_name" =>  $lastName,
                    "company" => null,
                    "phone" => $sale->customer->telephone ?? "",
                    "address1" => $sale->delivery->street ?? "",
                    "address2" => null,
                    "city" => $sale->delivery->city ?? "",
                    "province" => $sale->delivery->neighborhood ?? "",
                    "province_code" => $sale->delivery->state ?? "",
                    "country" => "Brazil",
                    "country_code" => "BR",
                    "zip" => $sale->delivery->zipcode ?? "",
                    "latitude" => null,
                    "longitude" => null
                ],
                "line_items" => $dataProducts,
                "currency" => "BRL",
                "total_price" => $totalValue,
                "subtotal_price" => $subtotal,
                "payment_status" => $status, // PAID, PENDING, NOT_PAID
                "payment_method" => $paymentMethod, // BOLETO, CREDIT_CARD, DEPOSIT, PIX, OTHER
                "tracking_numbers" => !empty($trackingCodes) ? implode(",", $trackingCodes) : null,
                "referring_site" => "https://" . $domainName,
                "status_url" => $checkoutLink,
                "billet_url" => $boletoLink,
                "billet_line" => $sale->boleto_digitable_line, // Linha digitável do boleto / Pix Copia e Cola
                "billet_expired_at" => $sale->boleto_due_date,
                "original_created_at" => $sale->created_at->format("Y-m-d H:i:s")
            ];

            $return = $this->sendPostApi($data);

            if (isset($return["result"]["success"]) && $return["result"]["success"] == true) {
                $sentStatus = 2;
            } else {
                $sentStatus = 1;
            }

            ReportanaSent::create([
                "data" => json_encode($data),
                "response" => json_encode($return),
                "sent_status" => $sentStatus,
                "instance_id" => $sale->id,
                "instance" => "sale",
                "event_sale" => (new ReportanaIntegration())->present()->getEvent($eventSale),
                "reportana_integration_id" => $this->integrationId,
            ]);

            return $return;
        } catch (Exception $e) {
            Log::warning("Erro ao enviar notificação para Reportana na venda de recuperação " . $sale->id);

            report($e);
        }
    }

    /**
     * @param $sale
     * @param $planSales
     * @param $domain
     * @param $eventSale
     * @param bool $trackingCreatedEvent
     * @return array
     */
    public function sendSale($sale, $planSales, $domain, $eventSale, bool $trackingCreatedEvent = false)
    {
        try {
            if (!empty($domain)) {
                $checkout = $sale->checkout;

                $dataProducts = [];
                foreach ($planSales as $planSale) {
                    $dataProducts[] = [
                        "id" => $planSale->plan_id,
                        "name" => $planSale->plan->name,
                        "price" => $planSale->plan->price,
                        "qty" => $planSale->amount,
                        "image" => $planSale->plan->products->first()->photo ?? "",
                    ];
                }

                $trackingCodes = $sale->trackings
                    ->pluck("tracking_code")
                    ->values()
                    ->toArray();

                $status = "";
                switch ($eventSale) {
                    case "billet_pending":
                    case "pix_pending":
                        $status = "pending";
                        break;
                    case "credit_card_paid":
                    case "billet_paid":
                    case "pix_paid":
                        $status = "paid";
                        break;
                    case "credit_card_refused":
                        $status = "refused";
                        break;
                    case "abandoned_cart":
                        $status = "abandoned_cart";
                        break;
                    case "billet_expired":
                    case "pix_expired":
                        $status = "expired";
                        break;
                }

                $totalValue = number_format(($sale->original_total_paid_value - $sale->interest_total_value) / 100, 2, ".", "");
                $subtotal = number_format($sale->sub_total, 2, ".", "");

                $domainName = $domain->name ?? "nexuspay.vip";
                $boletoLink =
                    "https://checkout.{$domainName}/order/" .
                    Hashids::connection("sale_id")->encode($sale->id) .
                    "/download-boleto";

                $data = [
                    "type" => "order",
                    "event_type" => $trackingCreatedEvent ? "tracking_created" : $eventSale,
                    "payment_type" => (new Sale())->present()->getPaymentType($sale->payment_method),
                    "order" => [
                        "financial_status" => $status,
                        "billet_url" => $boletoLink,
                        "gateway" => "cloudfox",
                        "checkout_url" =>
                        "https://checkout." . $domain->name . "/recovery/" . Hashids::encode($sale->checkout_id),
                        "id" => $sale->checkout_id,
                        "status" => $status,
                        "codigo_barras" => $sale->boleto_digitable_line,
                        "boleto_due_date" => $sale->boleto_due_date,
                        "shopify_reference" => $sale->shopify_order,
                        "src" => $checkout->src ?? "",
                        "utm_source" => $checkout->utm_source ?? "",
                        "utm_medium" => $checkout->utm_medium ?? "",
                        "utm_campaign" => $checkout->utm_campaign ?? "",
                        "utm_term" => $checkout->utm_term ?? "",
                        "utm_content" => $checkout->utm_content ?? "",
                        "values" => [
                            "subtotal" => $subtotal,
                            "total" => $totalValue,
                        ],
                        "costumer" => [
                            "name" => $sale->customer->name,
                            "email" => $sale->customer->email,
                            "doc" => $sale->customer->document,
                            "phone_number" => preg_replace("/[^0-9]/", "", $sale->customer->telephone),
                            "address" => $sale->delivery->street,
                            "address_number" => $sale->delivery->number,
                            "address_comp" => $sale->delivery->complement,
                            "address_district" => $sale->delivery->neighborhood,
                            "address_city" => $sale->delivery->city,
                            "address_state" => $sale->delivery->state,
                            "address_country" => $sale->delivery->country,
                            "address_zip_code" => preg_replace("/[^0-9]/", "", $sale->delivery->zip_code),
                        ],
                        "products" => $dataProducts,
                        "tracking_codes" => $trackingCodes,
                    ],
                    "created_at" => $sale->start_date,
                    "updated_at" => Carbon::createFromFormat("Y-m-d H:i:s", $sale->updated_at)->toDateTimeString(),
                ];

                $return = $this->sendPost($data);
                if (isset($return["code"]) && $return["code"] > 199 && $return["code"] < 300) {
                    $sentStatus = 2;
                } else {
                    $sentStatus = 1;
                }
                ReportanaSent::create([
                    "data" => json_encode($data),
                    "response" => json_encode($return),
                    "sent_status" => $sentStatus,
                    "instance_id" => $sale->id,
                    "instance" => "sale",
                    "event_sale" => (new ReportanaIntegration())->present()->getEvent($eventSale),
                    "reportana_integration_id" => $this->integrationId,
                ]);

                return $return;
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
