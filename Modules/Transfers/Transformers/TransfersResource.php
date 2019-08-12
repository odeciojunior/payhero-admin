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
        $value = number_format(intval($this->value) / 100, 2, ',', '.');

        return [
            'id'             => Hashids::encode($this->id),
            'type'           => $this->type,
            'type_enum'      => $this->type_enum,
            'value'          => $this->currency == 'dolar' ? '$ ' . $value : 'R$ ' . $value,
            'reason'         => (!empty($this->transaction) && empty($this->reason)) ? 'Transação #' : $this->reason,
            'transaction_id' => strtoupper(Hashids::connection('sale_id')->encode($this->sale)),
            'sale_id'        => Hashids::connection('sale_id')->encode($this->sale),
            'date'           => $this->created_at->format('d/m/Y'),
        ];
    }
}
