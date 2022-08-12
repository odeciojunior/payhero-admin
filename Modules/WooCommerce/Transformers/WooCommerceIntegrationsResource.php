<?php

namespace Modules\WooCommerce\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class WooCommerceIntegrationsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "project_id" => Hashids::encode($this->project_id),
            "user_id" => Hashids::encode($this->user_id),
            "token_user" => $this->token_user,
            "token_pass" => $this->token_pass,
            "status" => $this->status,
        ];
    }
}
