<?php

namespace Modules\DiscountCoupons\Transformers;

use Illuminate\Support\Facades\Lang;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class DiscountCouponsResource extends Resource
{
    public function toArray($request)
    {

        return [
            'id'                => Hashids::encode($this->id),
            'nome'              => $this->nome,
            'tipo'              => $this->tipo,
            'valor'             => $this->valor,
            'cod_cupom'         => $this->cod_cupom,
            'status'            => $this->status,
            'status_translated' => Lang::get('definitions.enum.status.' . $this->present()->getStatus($this->status)),
        ];
    }
}
