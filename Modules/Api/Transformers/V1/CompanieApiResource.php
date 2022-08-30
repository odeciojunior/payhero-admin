<?php

namespace Modules\Api\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanieApiResource extends JsonResource
{
    public function toArray($request)
    {
        return [];
    }
}
