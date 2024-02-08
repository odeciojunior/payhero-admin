<?php

namespace Modules\Pixels\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PixelEditResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id_code" => $this->id_code,
            "name" => $this->name,
            "platform" => $this->platform,
            "code" => $this->code,
            "status" => $this->status,
            "checkout" => $this->checkout ? "true" : "false",
            "purchase_all" => $this->purchase_all ? "true" : "false",
            "basic_data" => $this->basic_data ? "true" : "false",
            "delivery" => $this->delivery ? "true" : "false",
            "coupon" => $this->coupon ? "true" : "false",
            "payment_info" => $this->payment_info ? "true" : "false",
            "purchase_card" => $this->purchase_card ? "true" : "false",
            "purchase_boleto" => $this->purchase_boleto ? "true" : "false",
            "purchase_pix" => $this->purchase_pix ? "true" : "false",
            "upsell" => $this->upsell ? "true" : "false",
            "purchase_upsell" => $this->purchase_upsell ? "true" : "false",
            "apply_on_plans" => $this->apply_on_plans,
            "purchase_event_name" => $this->purchase_event_name,
            "is_api" => $this->is_api,
            "facebook_token" => $this->facebook_token,
            "url_facebook_domain" => $this->url_facebook_domain,
            "percentage_purchase_boleto_enabled" => $this->percentage_purchase_boleto_enabled,
            "value_percentage_purchase_boleto" => $this->value_percentage_purchase_boleto,
            "percentage_purchase_pix_enabled" => $this->percentage_purchase_pix_enabled,
            "value_percentage_purchase_pix" => $this->value_percentage_purchase_pix,
            "event_select" => $this->event_select,
            "send_value_checkout" => $this->send_value_checkout ? "true" : "false",
        ];
    }
}
