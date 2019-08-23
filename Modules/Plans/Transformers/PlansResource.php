<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Lang;

class PlansResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id_code,
            'name'              => $this->name,
            'description'       => $this->description,
            'code'              => isset($this->projectId->domains[0]->name) ? 'https://checkout.' . $this->projectId->domains[0]->name . '/' . $this->code : 'Dominio não configurado',
            'price'             => 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", $this->price)) / 100, 2, ',', '.'),
            'status'            => isset($this->projectId->domains[0]->name) ? 1 : 0,
            'status_code'       => $this->status,
            'status_translated' => Lang::get('definitions.enum.plan.status.' . $this->getEnum('status', $this->status)),
        ];
    }
}
