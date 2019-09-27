<?php

namespace Modules\Deliveries\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

/**
 * @property mixed id_code
 * @property mixed state
 * @property mixed zip_code
 * @property mixed city
 * @property mixed country
 * @property mixed neighborhood
 * @property mixed number
 * @property mixed street
 * @property mixed complement
 */
class DeliveryResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'code'         => $this->id_code,
            'zip_code'     => $this->zip_code,
            'country'      => $this->country,
            'state'        => $this->state,
            'city'         => $this->city,
            'neighborhood' => $this->neighborhood,
            'street'       => $this->street,
            'number'       => $this->number,
            'complement'   => $this->complement,
        ];
    }
}
