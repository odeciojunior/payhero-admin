<?php

namespace Modules\DiscountCoupons\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class DiscountCouponsResource extends Resource
{
    public function toArray($request)
    {

        return [
            'id'     => Hashids::encode($this->id),
            'name'   => $this->name,
            'type'   => $this->type == 0 ? 'Porcentagem' : 'Valor',
            'value'  => $this->type == 0 ? $this->value : number_format(intval($this->value) / 100, 2, ',', '.'), 
            'code'   => $this->code,
            'status' => $this->status,
        ];
    }
}
