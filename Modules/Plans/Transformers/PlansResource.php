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
            'code'        => Hashids::encode($this->id),
            'price'       => 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", $this->price)) / 100, 2, ',', '.'),
            'status'      => $this->status,
        ];
    }
}
