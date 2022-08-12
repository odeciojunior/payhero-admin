<?php

namespace Modules\Reportana\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class ReportanaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "project_id" => Hashids::encode($this->project->id),
            "project_name" => mb_substr($this->project->name, 0, 20),
            "project_photo" => $this->project->photo,
            "url_api" => $this->url_api,
            "boleto_generated" => $this->billet_generated,
            "boleto_paid" => $this->billet_paid,
            "boleto_expired" => $this->billet_expired,
            "credit_card_refused" => $this->credit_card_refused,
            "credit_card_paid" => $this->credit_card_paid,
            "pix_generated" => $this->pix_generated,
            "pix_paid" => $this->pix_paid,
            "pix_expired" => $this->pix_expired,
            "abandoned_cart" => $this->abandoned_cart,
            "created_at" => $this->created_at->format("d/m/Y"),
        ];
    }
}
