<?php

namespace Modules\Plans\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class PlansResource extends Resource
{
    public function toArray($request)
    {

        return [
            'id'          => Hashids::encode($this->id),
            'name'        => $this->name,
            'description' => $this->description,
            'code'        => $this->code,
            'price'       => $this->price,
            'status'      => $this->status,
        ];
    }
}
