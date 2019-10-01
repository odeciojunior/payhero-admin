<?php

namespace Modules\Projects\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

/**
 * @property mixed id
 * @property mixed name
 */
class ProjectsSelectResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'   => Hashids::encode($this->id),
            'name' => $this->name,
        ];
    }
}
