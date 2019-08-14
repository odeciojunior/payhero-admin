<?php

namespace Modules\Transfers\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class TransfersResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $anticipableValue = '';
        if (!empty($this->anticipation_id)) {
            $anticipableValue = ' ( R$ ' . number_format(intval($this->antecipable_value) / 100, 2, ',', '.') . ' antecipado em ' . Carbon::createFromFormat('Y-m-d H:i:s', $this->anticipationCreatedAt)
                                                                                                                                          ->format('d/m/Y') . ')';
        }

        $value = number_format(intval($this->value) / 100, 2, ',', '.');

        return [
            'id'                => Hashids::encode($this->id),
            'type'              => $this->type,
            'type_enum'         => $this->type_enum,
            'anticipable_value' => $anticipableValue,
            'value'             => $this->currency == 'dolar' ? '$ ' . $value : 'R$ ' . $value,
            'reason'            => (!empty($this->transaction) && empty($this->reason)) ? 'Transação #' : $this->reason,
            'transaction_id'    => strtoupper(Hashids::connection('sale_id')->encode($this->sale)),
            'sale_id'           => Hashids::connection('sale_id')->encode($this->sale),
            'date'              => $this->created_at->format('d/m/Y'),
        ];
    }
}
