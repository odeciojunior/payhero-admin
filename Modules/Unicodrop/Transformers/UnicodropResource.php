<?php

namespace Modules\Unicodrop\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class UnicodropResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "project_id" => Hashids::encode($this->project->id),
            "project_name" => mb_substr($this->project->name, 0, 20),
            "project_photo" => $this->project->photo,
            "token" => $this->token,
            "boleto_generated" => $this->billet_generated,
            "boleto_paid" => $this->billet_paid,
            "credit_card_refused" => $this->credit_card_refused,
            "credit_card_paid" => $this->credit_card_paid,
            "abandoned_cart" => $this->abandoned_cart,
            "pix" => $this->pix,
            "created_at" => $this->created_at->format("d/m/Y"),
        ];
    }
}
