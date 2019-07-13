<?php

namespace Modules\Reports\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class SalesByOriginResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'origin'       => $this->origin,
            'sales_amount' => $this->sales_amount,
            'balance'      => $this->value,
        ];
    }
}
