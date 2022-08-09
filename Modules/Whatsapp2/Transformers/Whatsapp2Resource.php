<?php

namespace Modules\Whatsapp2\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class Whatsapp2Resource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "project_id" => Hashids::encode($this->project->id),
            "project_name" => mb_substr($this->project->name, 0, 20),
            "project_photo" => $this->project->photo,
            "api_token" => $this->api_token,
            "url_checkout" => $this->url_checkout,
            "url_order" => $this->url_order,
            "boleto_generated" => $this->billet_generated,
            "boleto_paid" => $this->billet_paid,
            "credit_card_refused" => $this->credit_card_refused,
            "credit_card_paid" => $this->credit_card_paid,
            "abandoned_cart" => $this->abandoned_cart,
            "pix_expired" => $this->pix_expired,
            "pix_paid" => $this->pix_paid,
            "created_at" => $this->created_at->format("d/m/Y"),
        ];
    }
}
