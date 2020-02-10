<?php

namespace Modules\Shipping\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Lang;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ShippingResource
 * @package Modules\Shipping\Transformers
 */
class ShippingResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'shipping_id'               => Hashids::encode($this->id),
            'name'                      => $this->name,
            'information'               => $this->information,
            'value'                     => $this->value == null || $this->type_enum != 1 ? 'Calculado automáticamente' : $this->value,
            'type'                      => Lang::get('definitions.enum.shipping.type.' . $this->present()->getTypeEnum($this->type_enum)),
            'zip_code_origin'           => $this->zip_code_origin,
            'status'                    => $this->status,
            'rule_value'                => number_format($this->rule_value / 100, 2, ',', '.'),
            'status_translated'         => Lang::get('definitions.enum.shipping.status.' . $this->present()->getStatus($this->status)),
            'pre_selected'              => $this->pre_selected,
            'pre_selected_translated'   => Lang::get('definitions.enum.shipping.pre_selected.' . $this->present()->getPreSelectedStatus($this->pre_selected)),
        ];
    }
}
