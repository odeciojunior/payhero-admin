<?php

namespace Modules\Products\Transformers;

use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Product;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => Hashids::encode($this->id),
            'name'              => $this->name,
            'name_short'        => Str::limit($this->name, 14),
            'name_short_flag'   => mb_strwidth($this->name, 'UTF-8') <= 14 ? false : true,
            'description'       => Str::limit($this->description, 28),
            'type_enum'         => $this->type_enum,
            'status_enum'       => $this->status_enum,
            'cost'              => 'R$ '.number_format(($this->productsPlans->first()->cost ?? 0) / 100, 2, '.', ','),
            'photo'             => !empty($this->photo) ? $this->photo : 'https://cloudfox-digital-products.s3.amazonaws.com/public/global/img/produto.svg',
            'qtd_variants'      => count($this->variants)
        ];
    }
}
