<?php

namespace Modules\Reports\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class SalesByOriginResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'origin'       => $this->origin ?? 0,
            'sales_amount' => $this->sales_amount ?? 0,
            'balance'      => isset($this->value) ? number_format(intval($this->value) / 100, 2, ',', '.') : 0,
        ];
    }
}
