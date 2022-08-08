<?php

namespace Modules\Chargebacks\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CompanyResource
 * @package Modules\Companies\Transformers
 */
class OldChargebackResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "name" => $this->name,
            "fantasy_name" => $this->fantasy_name,
            "count_sales_approved" => $this->contSalesApproved ?? 0,
            "count_sales_chargeback" => $this->contSalesChargeBack ?? 0,
            "chargeback_tax" => $this->chargebackTax ? $this->chargebackTax . "%" : "0,00%",
        ];
    }

    /**
     * @param mixed $offset
     * @return bool
     * @see https://github.com/laravel/framework/issues/29916
     */
    public function offsetExists($offset)
    {
        // array_key_exists($offset, $this->resource) is deprecated php7.4;
        return property_exists($this->resource, $offset);
    }
}
