<?php

namespace Modules\Products\Transformers;

use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Product;

class ProductsSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $cost =
            ($this->currency_type_enum == 1 ? 'R$ ' : '$ ') .
            number_format((!empty($this->cost) ? $this->cost : 0) / 100, 2, ".", ",");
        if (count($this->productsPlans) > 0) {
            $cost =
                ($this->productsPlans->first()->currency_type_enum == 1 ? 'R$ ' : '$ ') .
                number_format(($this->productsPlans->first()->cost ?? 0) / 100, 2, ".", ",");
        }

        return [
            "id" => Hashids::encode($this->id),
            "name" => $this->name,
            "name_short" => Str::limit($this->name, 14),
            "name_short_flag" => mb_strwidth($this->name, "UTF-8") <= 14 ? false : true,
            "description" => Str::limit($this->description, 22),
            "currency_type_enum" => $this->productsPlans->first()->currency_type_enum ?? $this->currency_type_enum,
            "type_enum" => $this->type_enum,
            "status_enum" => $this->status_enum,
            "cost" => $cost,
            "photo" => !empty($this->photo)
                ? $this->photo
                : "https://cloudfox-digital-products.s3.amazonaws.com/public/global/img/produto.svg",
        ];
    }
}
