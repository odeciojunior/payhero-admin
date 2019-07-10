<?php

namespace Modules\Transfers\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class TransfersResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {

        return [
            'id'          => Hashids::encode($this->id),
            'type'        => $this->type,
            'value'       => $this->currency == 'dolar' ? '$ ' : 'R$ ' . number_format(intval($this->value) / 100, 2, ',', '.'),
            'description' => 'Transação ' . '#' . strtoupper(Hashids::connection('sale_id')->encode($this->sale)),
            'date'        => $this->created_at->format('d/m/Y'),
        ];

    }


}
