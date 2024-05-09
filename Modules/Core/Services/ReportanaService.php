<?php

namespace Modules\Core\Services;

use App\Services\Core\FoxUtils;
use Exception;
use Modules\Core\Entities\PixCharge;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\ReportanaSent;

class ReportanaService
{
    public $urlApi;

    public $apiVersion;

    public $client_id;

    public $client_secret;

    private $integrationId;

    function __construct($client_id, $client_secret, $integrationId)
    {
        $this->urlApi = "https://api.reportana.com";
        $this->apiVersion = "2022-05";
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->integrationId = $integrationId;
    }

    private function sendPost($url, $data)
    {
        if (!foxutils()->isProduction()) {
            return [
                "code" => 403,
                "result" => "Funcionalidade habilitada somente em ambiente de produção!",
            ];
        }

        $url = $this->urlApi . "/" . $this->apiVersion . $url;

        $data = json_encode($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $headers = [
            "Content-Type:application/json",
            "Content-Length:" . strlen($data),
            "Authorization:Basic " . base64_encode($this->client_id . ":" . $this->client_secret),
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            return [
                "code" => $httpCode,
                "result" => curl_error($ch),
            ];
        }

        curl_close($ch);

        return [
            "code" => $httpCode,
            "result" => json_decode($result, true),
        ];
    }

    private function sendPostApi($data)
    {
        if (!foxutils()->isProduction()) {
            return [
                "code" => 403,
                "result" => "Funcionalidade habilitada somente em ambiente de produção!",
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
            "Content-Type:application/json",
            "Content-Length:" . strlen($data),
            "Authorization:Basic " . base64_encode($this->client_id . ":" . $this->client_secret),
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch);

        if (curl_errno($ch)) {
            return [
                "code" => $httpCode,
                "result" => curl_error($ch),
            ];
        }

        curl_close($ch);

        return [
            "code" => $httpCode,
            "result" => json_decode($result, true),
        ];
    }

    public function sendSale($sale, $planSales, $domain, $eventSale, $trackingCreatedEvent = false, $recovery = false)
    {
        try {
            if (!empty($domain)) {
                $checkout = $sale->checkout;
                $pix = PixCharge::where("sale_id", $sale->id)->first();

                $dataProducts = [];
                $total = 0;
                foreach ($planSales as $planSale) {
                    $dataProducts[] = [
                        "title" => $planSale->plan->name,
                        "price" => $planSale->plan->price,
                        "quantity" => $planSale->amount,
                        "image_url" => $planSale->plan->products->first()->photo ?? "",
                        "path" => null,
                        "tracking_number" => null,
                    ];
                    $total += $planSale->plan->price * $planSale->amount;
                }

                $trackingCodes = $sale->trackings
                    ->pluck("tracking_code")
                    ->values()
                    ->toArray();

                $status = "";
                $paymentMethod = "OTHER";
                $domainName = $domain->name ?? "azcend.com.br";
                $boletoLink = null;
                $checkoutLink = null;

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
                        $boletoLink =
                            "https://checkout.{$domainName}/order/" .
                            hashids_encode($sale->id, "sale_id") .
                            "/download-boleto";
                        break;

                    case "billet_pending":
                        $status = "PENDING";
                        $paymentMethod = "BOLETO";
                        $boletoLink =
                            "https://checkout.{$domainName}/order/" .
                            hashids_encode($sale->id, "sale_id") .
                            "/download-boleto";
                        break;

                    case "billet_expired":
                        $status = "NOT_PAID";
                        $paymentMethod = "BOLETO";
                        $boletoLink =
                            "https://checkout.{$domainName}/order/" .
                            hashids_encode($sale->id, "sale_id") .
                            "/download-boleto";
                        break;

                    case "pix_paid":
                        $status = "PAID";
                        $paymentMethod = "PIX";
                        $checkoutLink =
                            "https://checkout.{$domainName}/recovery/" . $sale->checkout->id_code . "?recovery=true";

                        break;

                    case "pix_pending":
                        $status = "PENDING";
                        $paymentMethod = "PIX";
                        $checkoutLink =
                            "https://checkout.{$domainName}/recovery/" . $sale->checkout->id_code . "?recovery=true";

                        break;

                    case "pix_expired":
                        $status = "NOT_PAID";
                        $paymentMethod = "PIX";
                        $checkoutLink =
                            "https://checkout.{$domainName}/recovery/" . $sale->checkout->id_code . "?recovery=true";

                        break;
                }

                $totalValue = number_format(
                    ($sale->original_total_paid_value - $sale->interest_total_value) / 100,
                    2,
                    ".",
                    "",
                );

                $subtotal = number_format($sale->sub_total, 2, ".", "");

                $firstName = $sale->customer->name ? explode(" ", $sale->customer->name)[0] : "";
                $lastName = $sale->customer->name
                    ? explode(" ", $sale->customer->name)[count(explode(" ", $sale->customer->name)) - 1]
                    : "";

                $data = [
                    "reference_id" => hashids_encode($sale->id, "sale_id"),
                    "number" => strval($sale->id),
                    "admin_url" => "https://azcend.com.br/recovery",
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
                        "longitude" => null,
                    ],
                    "shipping_address" => [
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
                        "longitude" => null,
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
                    "billet_line" => $sale->boleto_digitable_line ?? ($pix->qrcode ?? ""), // Linha digitável do boleto / Pix Copia e Cola
                    "billet_expired_at" => $sale->boleto_due_date,
                    "original_created_at" => $sale->created_at->format("Y-m-d H:i:s"),
                ];

                $return = $this->sendPost("/orders", $data);

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

    public function sendAbandoned($checkout, $planCheckouts, $domain, $log, $recovery = false)
    {
        try {
            $dataProducts = [];
            $total = 0;
            foreach ($planCheckouts as $planCheckout) {
                $dataProducts[] = [
                    "title" => $planCheckout->plan->name,
                    "price" => $planCheckout->plan->price,
                    "quantity" => $planCheckout->amount,
                    "image_url" => $planCheckout->plan->products->first()->photo ?? "",
                    "path" => null,
                    "tracking_number" => null,
                ];

                $total += $planCheckout->plan->price * $planCheckout->amount;
            }

            if (empty($domain)) {
                $domainName = "azcend.com.br";
            } else {
                $domainName = $domain->name;
            }

            if (empty(preg_replace("/[^0-9]/", "", $log->total_value))) {
                $totalValue = $total;
            } else {
                $totalValue = number_format(
                    preg_replace("/[,]/", ".", preg_replace("/[^0-9,]/", "", $log->total_value)),
                    2,
                );
            }

            $checkoutLink = "https://checkout." . $domainName . "/recovery/" . $checkout->id_code . "?recovery=true";

            $firstName = $log->name ? explode(" ", $log->name)[0] : "";
            $lastName = $log->name ? explode(" ", $log->name)[count(explode(" ", $log->name)) - 1] : "";

            $data = [
                "reference_id" => strval($checkout->id_code),
                "number" => strval($checkout->id),
                "reason_type" => null,
                "admin_url" => "https://azcend.com.br/recovery",
                "customer_name" => $log->name ?? "",
                "customer_email" => $log->email ?? "",
                "customer_phone" => preg_replace("/[^0-9]/", "", str_replace("+55", "", $log->telephone)) ?? "",
                "billing_address" => [
                    "name" => $log->name ?? "",
                    "first_name" => $firstName,
                    "last_name" > $lastName,
                    "company" => null,
                    "phone" => preg_replace("/[^0-9]/", "", str_replace("+55", "", $log->telephone)) ?? "",
                    "address1" => $log->street ?? "",
                    "address2" => null,
                    "city" => $log->city ?? "",
                    "province" => $log->neighborhood ?? "",
                    "province_code" => $log->state ?? "",
                    "country" => $checkout->country,
                    "country_code" => "BR",
                    "zip" => preg_replace("/[^0-9]/", "", $log->zip_code) ?? "",
                    "latitude" => null,
                    "longitude" => null,
                ],
                "shipping_address" => [
                    "name" => $log->name ?? "",
                    "first_name" => $firstName,
                    "last_name" > $lastName,
                    "company" => null,
                    "phone" => preg_replace("/[^0-9]/", "", str_replace("+55", "", $log->telephone)) ?? "",
                    "address1" => $log->street ?? "",
                    "address2" => null,
                    "city" => $log->city ?? "",
                    "province" => $log->neighborhood ?? "",
                    "province_code" => $log->state ?? "",
                    "country" => $checkout->country,
                    "country_code" => "BR",
                    "zip" => preg_replace("/[^0-9]/", "", $log->zip_code) ?? "",
                    "latitude" => null,
                    "longitude" => null,
                ],
                "line_items" => $dataProducts,
                "currency" => "BRL",
                "total_price" => $totalValue,
                "subtotal_price" => $totalValue,
                "referring_site" => "https://" . $domainName,
                "checkout_url" => $checkoutLink,
                "completed_at" => null,
                "original_created_at" => $checkout->created_at->format("Y-m-d H:i:s"),
            ];

            $return = $this->sendPost("/abandoned-checkouts", $data);

            if (isset($return["code"]) && $return["code"] > 199 && $return["code"] < 300) {
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
            report($e);
        }
    }
}
