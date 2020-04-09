<?php

namespace Modules\Plans\Transformers;

use Modules\Core\Services\CompanyService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class PlansSelectResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => Hashids::encode($this->id),
            'name'       => $this->name,
        ];
    }
}
