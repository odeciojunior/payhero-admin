<?php

namespace Modules\Relatorios\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class VendasResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            //'comprador.nome as nome',
            'forma' => $this->forma_pagamento,
            'status' => $this->pagamento_statusas,
            'data_inicio' => $this->data_inicio,
            'data_finalizada' => $this->data_finalizada,
            'total_pago' => $this->valor_total_pago,
        ];

    }
}
