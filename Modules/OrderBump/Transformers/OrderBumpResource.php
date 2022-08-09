<?php

namespace Modules\OrderBump\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class OrderBumpResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "description" => $this->description,
            "active_flag" => $this->active_flag,
        ];
    }
}
