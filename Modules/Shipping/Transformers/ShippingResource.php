<?php

namespace Modules\Shipping\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Lang;
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
            'shipping_id'             => Hashids::encode($this->id),
            'name'                    => $this->name,
            'information'             => $this->information,
            'value'                   => $this->value == null ? 'Calculado automaticamente' : $this->value,
            'type'                    => $this->type == 'static' ? 'Estático' : ($this->type == 'sedex' ? 'SEDEX - Calculado automaticamente' : 'PAC - Calculado automaticamente'),
            'zip_code_origin'         => $this->zip_code_origin,
            'status'                  => $this->status,
            'status_translated'       => Lang::get('definitions.enum.shipping.status.' . $this->present()
                                                                                              ->getStatus($this->status)),
            'pre_selected'            => $this->pre_selected,
            'pre_selected_translated' => Lang::get('definitions.enum.shipping.pre_selected.' . $this->present()
                                                                                                    ->getPreSelectedStatus($this->pre_selected)),
        ];
    }
}
