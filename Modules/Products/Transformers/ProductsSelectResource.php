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
        if (empty($this->id)) {
            $product = Product::with('productsPlans')
            ->where('user_id', auth()->user()->account_owner_id)
            ->where('shopify_id', $this->shopify_id)
            ->first();

            return [
                'id'                => Hashids::encode($product->id),
                'name'              => $product->name,
                'name_short'        => Str::limit($product->name, 14),
                'name_short_flag'   => mb_strwidth($product->name, 'UTF-8') <= 14 ? false : true,
                'description'       => Str::limit($product->description, 28),
                'type_enum'         => $product->type_enum,
                'status_enum'       => $product->status_enum,
                'cost'              => 'R$ '.number_format(($product->productsPlans->first()->cost ?? 0) / 100, 2, '.', ','),
                'photo'             => !empty($product->photo) ? $product->photo : 'https://cloudfox-digital-products.s3.amazonaws.com/public/global/img/produto.svg',
                'qtd_variants'      => count($product->variants)
            ];
        }

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
        ];
    }
}
