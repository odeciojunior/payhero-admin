<?php

namespace Modules\Pixels\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PixelEditResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_code' => $this->id_code,
            'name' => $this->name,
            'platform' => $this->platform,
            'code' => $this->code,
            'status' => $this->status,
            'checkout' => $this->checkout ? 'true' : 'false',
            'purchase_boleto' => $this->purchase_boleto ? 'true' : 'false',
            'purchase_card' => $this->purchase_card ? 'true' : 'false',
            'purchase_pix' => $this->purchase_pix ? 'true' : 'false',
            'apply_on_plans' => $this->apply_on_plans,
            'code_meta_tag_facebook' => $this->code_meta_tag_facebook,
            'purchase_event_name' => $this->purchase_event_name,
            'is_api' => $this->is_api,
            'facebook_token' => $this->facebook_token,
            'value_percentage_purchase_boleto' => $this->value_percentage_purchase_boleto,
        ];
    }
}
