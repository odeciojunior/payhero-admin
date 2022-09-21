<?php

namespace Modules\Shipping\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\Plan;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ShippingResource
 * @package Modules\Shipping\Transformers
 */
class ShippingResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $this->apply_on_plans = json_decode($this->apply_on_plans);
        $this->not_apply_on_plans = json_decode($this->not_apply_on_plans);

        $selectPlans = ["id", "name", "description"];
        if ($this->use_variants) {
            $rawVariants = DB::raw(
                "(select count(distinct p.shopify_variant_id) from plans p where p.shopify_id = plans.shopify_id and p.shopify_id is not null and p.deleted_at is null) as variants"
            );
            $selectPlans[] = $rawVariants;
        }

        if ($this->apply_on_plans[0] === "all") {
            $this->apply_on_plans = collect()->push(
                (object) [
                    "id" => "all",
                    "name" => "Qualquer " . ($this->use_variants ? "plano" : "produto"),
                    "description" => "",
                    "variants" => 0,
                ]
            );
        } else {
            $this->apply_on_plans = Plan::select($selectPlans)
                ->whereIn("id", $this->apply_on_plans)
                ->get();
        }

        $this->not_apply_on_plans = Plan::select($selectPlans)
            ->whereIn("id", $this->not_apply_on_plans)
            ->get();
        if ($this->regions_values) {
            $this->value = "Por região";
        }
        return [
            "id_code" => Hashids::encode($this->id),
            "shipping_id" => Hashids::encode($this->id),
            "name" => $this->name,
            "regions_values" => $this->regions_values,
            "information" => $this->type_enum !== 4 ? $this->information : "Calculado automaticamente",
            "value" => $this->value == null || $this->type_enum != 1 ? "Calculado automaticamente" : $this->value,
            "type" => $this->present()->getTypeEnum($this->type_enum),
            "type_name" => Lang::get(
                "definitions.enum.shipping.type." . $this->present()->getTypeEnum($this->type_enum)
            ),
            "type_enum" => $this->type_enum,
            "zip_code_origin" => $this->zip_code_origin,
            "melhorenvio_integration_id" => Hashids::encode($this->melhorenvio_integration_id),
            "status" => $this->status,
            "rule_value" => number_format(($this->rule_value ?? 0) / 100, 2, ",", "."),
            "status_translated" => Lang::get(
                "definitions.enum.shipping.status." . $this->present()->getStatus($this->status)
            ),
            "pre_selected" => $this->pre_selected,
            "pre_selected_translated" => Lang::get(
                "definitions.enum.shipping.pre_selected." . $this->present()->getPreSelectedStatus($this->pre_selected)
            ),
            "use_variants" => $this->use_variants,
            "apply_on_plans" => $this->apply_on_plans->map(function ($plan) {
                return [
                    "id" => $plan->id === "all" ? "all" : Hashids::encode($plan->id),
                    "name" => $plan->name,
                    "description" => !empty($plan->variants) ? $plan->variants . " variantes" : $plan->description,
                ];
            }),
            "not_apply_on_plans" => $this->not_apply_on_plans->map(function ($plan) {
                return [
                    "id" => $plan->id === "all" ? "all" : Hashids::encode($plan->id),
                    "name" => $plan->name,
                    "description" => !empty($plan->variants) ? $plan->variants . " variantes" : $plan->description,
                ];
            }),
        ];
    }
}
