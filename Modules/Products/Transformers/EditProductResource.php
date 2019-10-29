<?php

namespace Modules\Products\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class EditProductResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $product    = [
            'id'          => $this->resource['product']->id_code,
            'name'        => $this->resource['product']->name,
            'photo'       => $this->resource['product']->photo ?? 'https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/product-default.png',
            'description' => $this->resource['product']->description,
            'sku'         => $this->resource['product']->sku,
            'category_id' => Hashids::encode($this->resource['product']->category_id),
            'shopify_id'  => $this->resource['product']->shopify_id,
            'cost'        => $this->resource['product']->cost,
            'price'       => $this->resource['product']->price,
            'width'       => $this->resource['product']->width,
            'height'      => $this->resource['product']->height,
            'weight'      => $this->resource['product']->weight,
        ];
        $categories = [];
        foreach ($this->resource['categories'] as $category) {
            $categories[] = [
                'id'   => $category->id_code,
                'name' => $category->name,
            ];
        }

        return [
            'product'    => $product,
            'categories' => $categories,
        ];
    }
}
