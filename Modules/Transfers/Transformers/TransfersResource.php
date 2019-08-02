<?php

namespace Modules\Transfers\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class TransfersResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id'             => Hashids::encode($this->id),
            'type'           => $this->type,
            'type_enum'      => $this->type_enum,
            'value'          => $this->currency == 'dolar' ? '$ ' : 'R$ ' . number_format(intval($this->value) / 100, 2, ',', '.'),
            'description'    => $this->company != null ? 'Transação ' . '#' : 'Saque',
            'transaction_id' => strtoupper(Hashids::connection('sale_id')->encode($this->sale)),
            'sale_id'        => Hashids::connection('sale_id')->encode($this->sale),
            'date'           => $this->created_at->format('d/m/Y'),
        ];
    }
}
