<?php

namespace Modules\Profile\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id_code'                     => $this->id_code,
            'name'                        => $this->name,
            'email'                       => $this->email,
            'cellphone'                   => $this->cellphone,
            'document'                    => $this->document,
            'zip_code'                    => $this->zip_code,
            'country'                     => $this->country,
            'state'                       => $this->state,
            'city'                        => $this->city,
            'neighborhood'                => $this->neighborhood,
            'street'                      => $this->street,
            'number'                      => $this->number,
            'complement'                  => $this->complement,
            'photo'                       => $this->complement,
            'date_birth'                  => $this->date_birth,
            'personal_document_status'    => $this->personal_document_status,
            'address_document_status'     => $this->address_document_status,
            'personal_document_translate' => $this->getEnum('personal_document_status', $this->personal_document_status, true),
            'address_document_translate'  => $this->getEnum('address_document_status', $this->address_document_status, true),
        ];
    }
}
