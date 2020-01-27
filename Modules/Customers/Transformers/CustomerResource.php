<?php

namespace Modules\Customers\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ClientResource
 * @property mixed name
 * @property mixed document
 * @property mixed email
 * @property mixed telephone
 * @property mixed id_code
 * @package Modules\Customers\Transformers
 */
class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'code'          => $this->id_code,
            'name'          => $this->name,
            'document'      => $this->document,
            'email'         => $this->email,
            'telephone'     => $this->telephone,
            'whatsapp_link' => $this->present()->getWhatsappLink(),
        ];
    }
}
