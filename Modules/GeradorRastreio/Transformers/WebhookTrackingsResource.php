<?php

namespace Modules\GeradorRastreio\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class WebhookTrackingsResource extends JsonResource
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
            "user_id" => Hashids::encode($this->user_id),
            "project_id" => Hashids::encode($this->project_id),
            "project_name" => $this->project->name,
            "project_photo" => $this->project->project_photo,
            "token_id" => Hashids::encode($this->token_id ?? null),
            "token" => $this->token->access_token ?? null,
            "clientid" => $this->clientid,
            "webhook_url" => $this->webhook_url,
            "credit_flag" => $this->credit_flag,
            "pix_flag" => $this->pix_flag,
            "billet_flag" => $this->billet_flag,
            "created_at" => $this->created_at->format("d/m/Y"),
            "updated_at" => $this->created_at->format("d/m/Y"),
        ];
    }
}
