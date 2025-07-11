<?php

namespace Modules\Reports\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesByOriginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "origin" => $this->origin ?? 0,
            "sales_amount" => $this->sales_amount ?? 0,
            "value" => foxutils()->formatMoney($this->value / 100),
        ];
    }
}
