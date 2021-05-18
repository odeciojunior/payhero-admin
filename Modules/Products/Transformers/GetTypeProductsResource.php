<?php

namespace Modules\Products\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class GetTypeProductsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'shopify'         => $this->resource['shopify'] != null ? '1' : '0',
            'productOriginal' => $this->resource['productOriginal'] != null ? '1' : '0',
            'projects'        => $this->resource['projects'],
        ];
    }
}
