<?php

namespace Modules\Customers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'code' => $this->id_code,
            'name' => $this->name,
            'document' => foxutils()->mask($this->document, '###.###.###-##'),
            'email' => $this->present()->getEmail(),
            'telephone' => foxutils()->getTelephone($this->telephone),
            'whatsapp_link' => $this->present()->getWhatsappLink(),
        ];
    }
}
