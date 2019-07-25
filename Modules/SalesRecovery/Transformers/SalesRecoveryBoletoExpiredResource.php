<?php

namespace Modules\SalesRecovery\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class SalesRecoveryBoletoExpiredResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        dd($this);

        return parent::toArray($request);
    }
}
