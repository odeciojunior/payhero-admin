<?php

namespace Modules\Shipping\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class ShippingResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'shipping_id'     => Hashids::encode($this->id),
            'name'            => $this->name,
            'information'     => $this->information,
            'value'           => $this->value,
            'type'            => $this->type == 'static' ? 'Estatico' : 'Calculado automaticamente',
            'zip_code_origin' => $this->zip_code_origin,
            'status'          => $this->status,
            'pre_selected'    => $this->pre_selected,
        ];
    }
}
