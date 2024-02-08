<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\Project;

class PixelService
{
    public const EVENTS = [
        "checkout",
        "purchase_all",
        "basic_data",
        "delivery",
        "coupon",
        "payment_info",
        "purchase_card",
        "purchase_boleto",
        "purchase_pix",
        "upsell",
        "purchase_upsell",
    ];

    public function store($projectId, $dataValidated): array
    {
        if (!empty($dataValidated["affiliate_id"])) {
            $dataValidated["affiliate_id"] = hashids_decode($dataValidated["affiliate_id"]);
            $affiliateId = $dataValidated["affiliate_id"];
        } else {
            $affiliateId = 0;
            $dataValidated["affiliate_id"] = null;
        }

        $project = Project::find(hashids_decode($projectId));

        if (!Gate::allows("edit", [$project, $affiliateId])) {
            return [
                "status" => 403,
                "message" => __("controller.pixel.permission.create"),
            ];
        }

        if (str_contains($dataValidated["code"], "script")) {
            return [
                "message" => "Informe somente o código do Pixel",
                "status" => 400,
            ];
        }

        $applyPlanEncoded = json_encode(foxutils()->getApplyPlans($dataValidated["add_pixel_plans"]));

        if ($dataValidated["platform"] == "google_adwords") {
            $this->dataGoogleAds($dataValidated);
        }

        if (
            in_array($dataValidated["platform"], ["taboola", "outbrain"]) &&
            empty($dataValidated["purchase-event-name"])
        ) {
            $dataValidated["purchase-event-name"] =
                $dataValidated["platform"] == "taboola" ? "make_purchase" : "Purchase";
        }

        if (!in_array($dataValidated["platform"], ["taboola", "outbrain"])) {
            $dataValidated["purchase-event-name"] = null;
        }

        if(empty($dataValidated["url_facebook_domain_edit"])){
            $dataValidated["url_facebook_domain_edit"] = null;
        }

        $facebookToken = null;
        $isApi = false;
        $facebookDomainUrl = null;
        if (
            $dataValidated["platform"] == "facebook" &&
            !empty($dataValidated["api-facebook"]) &&
            $dataValidated["api-facebook"] == "api"
        ) {
            $facebookToken = $dataValidated["facebook-token-api"];
            $isApi = true;
            $facebookDomainUrl = $dataValidated["url_facebook_domain"];
        }

        if (empty($dataValidated["value_percentage_purchase_boleto"])) {
            $dataValidated["value_percentage_purchase_boleto"] = 100;
        }

        if (empty($dataValidated["value_percentage_purchase_pix"])) {
            $dataValidated["value_percentage_purchase_pix"] = 100;
        }



        Pixel::create([
            "project_id" => $project->id,
            "name" => $dataValidated["name"],
            "code" => $dataValidated["code"],
            "platform" => $dataValidated["platform"],
            "status" => $dataValidated["status"] == "true",
            "checkout" => $dataValidated["checkout"] == "true",
            "send_value_checkout" => $dataValidated["send_value_checkout"] == "true",
            "purchase_all" => $dataValidated["purchase_all"] == "true",
            "basic_data" => $dataValidated["basic_data"] == "true",
            "delivery" => $dataValidated["delivery"] == "true",
            "coupon" => $dataValidated["coupon"] == "true",
            "payment_info" => $dataValidated["payment_info"] == "true",
            "purchase_card" => $dataValidated["purchase_card"] == "true",
            "purchase_boleto" => $dataValidated["purchase_boleto"] == "true",
            "purchase_pix" => $dataValidated["purchase_pix"] == "true",
            "upsell" => $dataValidated["upsell"] == "true",
            "purchase_upsell" => $dataValidated["purchase_upsell"] == "true",
            "affiliate_id" => $dataValidated["affiliate_id"],
            "campaign_id" => null,
            "apply_on_plans" => $applyPlanEncoded,
            "purchase_event_name" => $dataValidated["purchase-event-name"],
            "facebook_token" => $facebookToken,
            "is_api" => $isApi,
            "percentage_purchase_boleto_enabled" => $dataValidated["percentage_purchase_boleto_enabled"] == "true",
            "value_percentage_purchase_boleto" => $dataValidated["value_percentage_purchase_boleto"],
            "percentage_purchase_pix_enabled" => $dataValidated["percentage_purchase_pix_enabled"] == "true",
            "value_percentage_purchase_pix" => $dataValidated["value_percentage_purchase_pix"],
            "url_facebook_domain" => $facebookDomainUrl,
        ]);

        return [
            "status" => 200,
            "message" => "Pixel " . __("controller.success.create"),
        ];
    }

    public function update($pixelId, $dataValidated): array
    {
        $pixel = Pixel::with("project")->find(hashids_decode($pixelId));

        if (empty($pixel)) {
            return [
                "message" => "Pixel não encontrado",
                "status" => 400,
            ];
        }

        $project = $pixel->project;
        if (str_contains($dataValidated["code"], "script")) {
            return [
                "message" => "Informe somente o código do Pixel",
                "status" => 400,
            ];
        }

        $affiliateId = !empty($pixel->affiliate_id) ? $pixel->affiliate_id : 0;

        if (!Gate::allows("edit", [$project, $affiliateId])) {
            return ["message" => "Sem permissão para atualizar pixels", "status" => 403];
        }

        if ($dataValidated["platform"] == "google_adwords") {
            $dataValidated["code"] = str_replace(["AW-"], "", $dataValidated["code"]);
        }

        if (!in_array($dataValidated["platform"], ["taboola", "outbrain"])) {
            $dataValidated["purchase_event_name"] = null;
        }

        if (empty($dataValidated["purchase_event_name"])) {
            if ($dataValidated["platform"] == "taboola" && empty($pixel->taboola_conversion_name)) {
                $dataValidated["purchase_event_name"] = "make_purchase";
            } elseif ($dataValidated["platform"] == "outbrain" && empty($pixel->outbrain_conversion_name)) {
                $dataValidated["purchase_event_name"] = "Purchase";
            }
        }

        if(empty($dataValidated["url_facebook_domain_edit"])){
            $dataValidated["url_facebook_domain_edit"] = null;
        }

        if ($dataValidated["platform"] == "facebook") {
            $dataValidated["purchase_event_name"] = "";
            if ($dataValidated["is_api"] == "api") {
                $dataValidated["is_api"] = true;
            } else {
                $dataValidated["is_api"] = false;
                $dataValidated["facebook_token_api"] = null;
            }
        } else {
            $dataValidated["is_api"] = false;
        }

        if (empty($dataValidated["value_percentage_purchase_boleto"])) {
            $dataValidated["value_percentage_purchase_boleto"] = 100;
        }

        if (empty($dataValidated["value_percentage_purchase_pix"])) {
            $dataValidated["value_percentage_purchase_pix"] = 100;
        }

        if ($dataValidated["platform"] == "google_adwords") {
            $this->dataGoogleAds($dataValidated);
        }

        $applyPlanEncoded = json_encode((new PlanService())->getPlansApplyDecoded($dataValidated["edit_pixel_plans"]));

        $pixel->update([
            "name" => $dataValidated["name"],
            "platform" => $dataValidated["platform"],
            "status" => $dataValidated["status"] == "true",
            "code" => $dataValidated["code"],
            "apply_on_plans" => $applyPlanEncoded,
            "checkout" => $dataValidated["checkout"] == "true",
            "send_value_checkout" => $dataValidated["send_value_checkout"] == "true",
            "purchase_all" => $dataValidated["purchase_all"] == "true",
            "basic_data" => $dataValidated["basic_data"] == "true",
            "delivery" => $dataValidated["delivery"] == "true",
            "coupon" => $dataValidated["coupon"] == "true",
            "payment_info" => $dataValidated["payment_info"] == "true",
            "purchase_card" => $dataValidated["purchase_card"] == "true",
            "purchase_boleto" => $dataValidated["purchase_boleto"] == "true",
            "purchase_pix" => $dataValidated["purchase_pix"] == "true",
            "upsell" => $dataValidated["upsell"] == "true",
            "purchase_upsell" => $dataValidated["purchase_upsell"] == "true",
            "purchase_event_name" => $dataValidated["purchase_event_name"] ?? null,
            "facebook_token" => $dataValidated["facebook_token_api"],
            "is_api" => $dataValidated["is_api"],
            "percentage_purchase_boleto_enabled" => $dataValidated["percentage_purchase_boleto_enabled"] == "true",
            "value_percentage_purchase_boleto" => $dataValidated["value_percentage_purchase_boleto"],
            "percentage_purchase_pix_enabled" => $dataValidated["percentage_purchase_pix_enabled"] == "true",
            "value_percentage_purchase_pix" => $dataValidated["value_percentage_purchase_pix"],
            "url_facebook_domain" => $dataValidated["url_facebook_domain_edit"],
        ]);

        return ["message" => "Sucesso", "status" => 200];
    }

    private function dataGoogleAds(&$dataValidated)
    {
        $dataValidated["code"] = str_replace(["AW-"], "", $dataValidated["code"]);

        if (!empty($dataValidated["event_select"])) {
            foreach (self::EVENTS as $EVENT) {
                $dataValidated[$EVENT] = $dataValidated["event_select"] == $EVENT;
            }
        }
    }
}
