<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class PlansResource extends Resource
{
    public function toArray($request)
    {

        return [
            'id'          => $this->id_code,
            'name'        => $this->name,
            'description' => $this->description,
            'code'        => 'https://checkout.' . $this->projectId->domains[0]->name . '/' . $this->code ?? $this->code,
            'price'       => 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", $this->price)) / 100, 2, ',', '.'),
            'status'      => isset($this->projectId->domains[0]->name) ? 1 : 0,
        ];
    }
}
