<?php

namespace Modules\Products\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class EditProductResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $urlExpirationTime = null;
        if ($this->resource["product"]->type_enum == 2) {
            if (!empty($this->resource["product"]->url_expiration_time)) {
                $urlExpirationTime = $this->resource["product"]->url_expiration_time;
            } else {
                $urlExpirationTime = 24;
            }
        }

        $product = [
            "id" => $this->resource["product"]->id_code,
            "name" => $this->resource["product"]->name,
            "photo" => $this->resource["product"]->photo,
            "description" => $this->resource["product"]->description,
            "sku" => $this->resource["product"]->sku,
            "category_id" => Hashids::encode($this->resource["product"]->category_id),
            "shopify_id" => $this->resource["product"]->shopify_id,
            "shopify_variant_id" => $this->resource["product"]->shopify_variant_id,
            "cost" => $this->resource["product"]->cost,
            "price" => $this->resource["product"]->price,
            "width" => $this->resource["product"]->width,
            "height" => $this->resource["product"]->height,
            "weight" => $this->resource["product"]->weight,
            "length" => $this->resource["product"]->length,
            "currency_type_enum" => $this->resource["product"]->currency_type_enum,
            "type_enum" => $this->resource["product"]->type_enum,
            "digital_product_url" => $this->resource["product"]->digital_product_url ?? "",
            "url_expiration_time" => $urlExpirationTime,
        ];
        $categories = [];
        foreach ($this->resource["categories"] as $category) {
            $categories[] = [
                "id" => $category->id_code,
                "name" => $category->name,
            ];
        }

        return [
            "product" => $product,
            "categories" => $categories,
        ];
    }
}
