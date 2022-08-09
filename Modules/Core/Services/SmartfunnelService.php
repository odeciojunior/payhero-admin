<?php

namespace Modules\Core\Services;

use Exception;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use Modules\Core\Entities\SmartfunnelSent;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\FoxUtils;

/**
 * Class SmartfunnelService
 * @package Modules\Core\Services
 */
class SmartfunnelService
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
     * SmartfunnelService constructor.
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

    /**
     * @param $sale
     * @param $planSales
     * @param $eventSale
     * @return array
     */
    public function sendSale($sale, $planSales, $eventSale)
    {
        try {
            $planSale = $planSales->first();

            if ($eventSale == "billet_pending") {
                $status = "pending";
                $paymentType = "billet";
            } elseif ($eventSale == "credit_card_paid") {
                $status = "approved";
                $paymentType = "credit_card";
            } elseif ($eventSale == "billet_paid") {
                $status = "approved";
                $paymentType = "billet";
            }

            $telephone = FoxUtils::onlyNumbers($sale->customer->telephone);
            $sale->customer->telephone = strlen($telephone) > 11 ? substr($telephone, 2) : $telephone;

            $data = [
                "prod" => Hashids::encode($planSale->plan_id),
                "prod_name" => $planSale->plan->name,
                "commission_amount" => $this->getComission($sale),
                "email" => $sale->customer->present()->getEmail(),
                "name" => $sale->customer->name,
                "first_name" => $sale->customer->present()->getFirstName(),
                "last_name" => $sale->customer->present()->getLastName(),
                "ddd" => $sale->customer->present()->getFormatTelephone(true, false),
                "phone" => $sale->customer->present()->getFormatTelephone(false, true),
                "status" => $status,
                "purchase_date" => $sale->start_date,
                "payment_type" => $paymentType,
                "utm_campaign" => $sale->checkout->utm_campaign,
            ];

            $return = $this->sendPost($data);
            if (isset($return["code"]) && $return["code"] > 199 && $return["code"] < 300) {
                $sentStatus = (new SmartfunnelSent())->present()->getSentStatus("success");
            } else {
                $sentStatus = (new SmartfunnelSent())->present()->getSentStatus("error");
            }
            SmartfunnelSent::create([
                "data" => json_encode($data),
                "response" => json_encode($return),
                "sent_status" => $sentStatus,
                "sale_id" => $sale->id,
                "event_sale" => (new SmartfunnelSent())->present()->getEvent($eventSale),
                "smartfunnel_integration_id" => $this->integrationId,
            ]);

            return $return;
        } catch (Exception $e) {
            report($e);
        }
    }

    private function getComission($sale)
    {
        if ($sale->payment_method == (new Sale())->present()->getPaymentType("credit_card")) {
            $producerCompany = UserProject::with("company")
                ->where("type_enum", (new UserProject())->present()->getTypeEnum("producer"))
                ->where("project_id", $sale->project_id)
                ->first()->company;

            $foxvalue =
                (int) ((($sale->original_total_paid_value - $sale->interest_total_value) / 100) *
                    $producerCompany->gateway_tax);
            $foxvalue += FoxUtils::onlyNumbers($producerCompany->transaction_tax);
            $foxvalue += $sale->interest_total_value;
        } else {
            $producerCompany = UserProject::with("company")
                ->where("type_enum", (new UserProject())->present()->getTypeEnum("producer"))
                ->where("project_id", $sale->project_id)
                ->first()->company;

            $foxvalue = (int) (($sale->original_total_paid_value / 100) * $producerCompany->gateway_tax);

            if (FoxUtils::onlyNumbers($sale->total_paid_value) < 4000) {
                $transactionTax = 300;
            } else {
                $transactionTax = $producerCompany->transaction_tax;
            }

            $foxvalue += FoxUtils::onlyNumbers($transactionTax);
        }

        $producerValue = (int) $sale->original_total_paid_value - $foxvalue;

        if (!empty($sale->affiliate_id)) {
            $affiliate = $sale->affiliate;
            if (!empty($affiliate) && $affiliate->status_enum == (new Affiliate())->present()->getStatus("active")) {
                $affiliateValue = intval(
                    (($sale->original_total_paid_value - $foxvalue) / 100) * $affiliate->percentage
                );
                $producerValue -= $affiliateValue;
            }
        }

        if ($sale->payment_method == (new Sale())->present()->getPaymentType("credit_card")) {
            $producerValue -= $sale->installment_tax_value;
        }
        return substr_replace($producerValue, ".", strlen($producerValue) - 2, 0);
    }
}
