<?php

namespace Modules\Api\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class SubsellersApiResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => Hashids::encode($this->id),
            'name' => $this->name,
            'document' => $this->document ?? null,
            'email' => $this->email,
            'cellphone' => $this->cellphone
        ];
    }
}
