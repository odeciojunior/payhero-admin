<?php

namespace Modules\Api\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleApiResource extends JsonResource
{
    public function toArray($request)
    {
        return [];
    }
}
