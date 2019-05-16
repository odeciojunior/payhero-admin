<?php

namespace Modules\Products\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ProductsResource extends Resource {

    public function toArray($request) {

        return [
            'id'          => Hashids::encode($this->id),
            'name'        => $this->name,
            'description' => $this->description,
            'photo'       => $this->photo,
            'created_at'  => $this->created_at
        ];
    }
}
