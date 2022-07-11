<?php

namespace Modules\Shipping\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingSelectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => hashids_encode($this->id),
            'name' => $this->name,
            'information' => $this->information,
            'type' => $this->type,
        ];
    }
}
