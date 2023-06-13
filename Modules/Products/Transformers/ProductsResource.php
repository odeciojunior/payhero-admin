<?php

namespace Modules\Products\Transformers;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductsResource
 * @package Modules\Products\Transformers
 */
class ProductsResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        // shopify
        if ($this->shopify == 1) {
            $this->id_view = 1;
        }
        // woocommerce
        elseif (
            $this->shopify == 0 and
            (!empty($this->shopify_id) and !empty($this->shopify_variant_id) or
                empty($this->shopify_id) and !empty($this->shopify_variant_id) or
                !empty($this->shopify_id) and empty($this->shopify_variant_id))
        ) {
            $this->id_view = 2;
        }
        // sirius
        elseif ($this->shopify == 0 and empty($this->shopify_id) and empty($this->shopify_variant_id)) {
            $this->id_view = 0;
        }

        return [
            "id" => $this->id_code,
            "id_view" => $this->id_view, //($this->shopify == 1 ? $this->shopify_id : $this->id_code),
            "name" => $this->name,
            "description" => $this->description,
            "image" =>
                $this->photo == ""
                    ? "https://nexuspay-digital-products.s3.amazonaws.com/admin/produto.svg"
                    : $this->photo,
            "link" => "/api/products/" . $this->id_code . "/edit",
            "created_at" => Carbon::parse($this->created_at)->format("d/m/Y"),
            "type_enum" => $this->type_enum,
            "status_enum" => $this->status_enum,
        ];
    }
}
