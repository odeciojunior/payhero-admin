<?php

namespace Modules\Companies\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

/**
 * Class CompanyResource
 * @package Modules\Companies\Transformers
 */
class CompanyResource extends Resource
{
    /**
     * @return int
     */
    private function documentStatus()
    {
        //criar logica para trazer o status correto
        return $this->getEnum('status', 2, true);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id_code'         => $this->id_code,
            'fantasy_name'    => $this->fantasy_name,
            'cnpj'            => $this->cnpj,
            'zip_code'        => $this->zip_code,
            'country'         => $this->country,
            'state'           => $this->state,
            'city'            => $this->city,
            'street'          => $this->street,
            'complement'      => $this->complement,
            'neighborhood'    => $this->neighborhood,
            'number'          => $this->number,
            'document_status' => $this->documentStatus(),
        ];
    }
}
