<?php

namespace Modules\Products\Transformers;

use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => !empty($this->description) ? $this->name . ' - ' . Str::limit($this->description, 20) : $this->name,
            'description' => Str::limit($this->description, 31),
            'name_substr' => Str::limit($this->name, 17),
            'type_enum'   => $this->type_enum,
            'status_enum' => $this->status_enum,
            'cost'        => 'R$ '.number_format(($this->productsPlans->first()->cost ?? 0) / 100, 2, '.', ','),
            'photo'       => !empty($this->photo) ? $this->photo : 'https://cloudfox-digital-products.s3.amazonaws.com/public/global/img/produto.svg'
        ];
    }
}
