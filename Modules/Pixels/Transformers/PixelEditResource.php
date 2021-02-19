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
            'checkout' => $this->checkout,
            'purchase_boleto' => $this->purchase_boleto,
            'purchase_card' => $this->purchase_card,
            'apply_on_plans' => $this->apply_on_plans,
            'code_meta_tag_facebook' => $this->code_meta_tag_facebook,
            'outbrain_conversion_name' => $this->outbrain_conversion_name,
            'taboola_conversion_name' => $this->taboola_conversion_name,
        ];
    }
}
