<?php

namespace Modules\AstronMembers\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class AstronMembersResource extends JsonResource
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
            'id'                  => Hashids::encode($this->id),
            'project_id'          => Hashids::encode($this->project->id),
            'project_name'        => substr($this->project->name, 0, 20),
            'project_photo'       => $this->project->photo,
            'link'                => $this->link,
            'token'               => $this->token,
            'boleto_generated'    => $this->boleto_generated,
            'boleto_paid'         => $this->boleto_paid,
            'credit_card_refused' => $this->credit_card_refused,
            'credit_card_paid'    => $this->credit_card_paid,
            'abandoned_cart'      => $this->abandoned_cart,
            'pix_generated'       => $this->pix_generated,
            'pix_paid'            => $this->pix_paid,
            'pix_expired'         => $this->pix_expired,
            'created_at'          => $this->created_at->format('d/m/Y'),
        ];
    }
}
