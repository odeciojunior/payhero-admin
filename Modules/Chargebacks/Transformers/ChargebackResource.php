<?php

namespace Modules\Chargebacks\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ChargebackResource
 * @package Modules\Companies\Transformers
 */
class ChargebackResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array,
     * @throws Exception
     */
    public function toArray($request)
    {
        $plansSale = $this->getRelation("sale")
            ->getRelation("plansSales")
            ->first();
        $plan = $plansSale ? $plansSale->getRelation("plan") : null;
        $project = $plan ? $plan->getRelation("project") : null;

        return [
            "id" => Hashids::encode($this->id),
            "transaction_id" => Hashids::connection("sale_id")->encode($this->sale_id),
            "sale_code" => "#" . Hashids::connection("sale_id")->encode($this->sale_id),
            "sale_id" => $this->sale_id,
            "company" => $this->company->fantasy_name,
            "user" => $this->user->name,
            "project" => $project->name ?? "",
            "product" =>
                count($this->getRelation("sale")->getRelation("plansSales")) > 1
                    ? "Carrinho"
                    : Str::limit($plan->name ?? "", 25),
            "customer" => $this->sale->customer->name ?? "",
            "transaction_date" => $this->transaction_date
                ? with(new Carbon($this->transaction_date))->format("d/m/Y")
                : "",
            "adjustment_date" => $this->adjustment_date
                ? with(new Carbon($this->adjustment_date))->format("d/m/Y")
                : "",
            "amount" => 'R$ ' . number_format(intval($this->amount) / 100, 2, ",", "."),
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
