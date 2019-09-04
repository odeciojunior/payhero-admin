<?php

namespace Modules\Products\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class GetTypeProductsResource extends Resource
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
