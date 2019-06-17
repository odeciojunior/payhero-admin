<?php

namespace Modules\Pixels\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class PixelsResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'nome'       => $this->name,
            'cod_pixel'  => $this->cod,
            'plataforma' => $this->platform,
            'status'     => $this->status,
        ];
    }
}
