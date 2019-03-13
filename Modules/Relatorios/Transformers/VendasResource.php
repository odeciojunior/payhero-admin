<?php

namespace Modules\Relatorios\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class VendasResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
