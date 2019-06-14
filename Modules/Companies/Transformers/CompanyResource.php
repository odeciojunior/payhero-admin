<?php

namespace Modules\Companies\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class CompanyResource extends Resource {

    public function toArray($request) {

        return [
            'id'           => Hashids::encode($this->id),
            'fantasy_name' => $this->fantasy_name,
            'cnpj'         => $this->cnpj,
            'zip_code'     => $this->zip_code,
            'country'      => $this->country,
            'state'        => $this->state,
            'city'         => $this->city,
            'street'       => $this->street,
            'complement'   => $this->complement,
            'neighborhood' => $this->neighborhood,
            'number'       => $this->number,
        ];

    }
}
