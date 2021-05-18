<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class PlansSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => Hashids::encode($this->id),
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
