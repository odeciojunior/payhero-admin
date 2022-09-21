<?php

namespace Modules\ProjectUpsellRule\Transformers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Shipping;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectsUpsellResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        $this->apply_on_shipping = json_decode($this->apply_on_shipping);
        $this->apply_on_plans = json_decode($this->apply_on_plans);
        $this->offer_on_plans = json_decode($this->offer_on_plans);

        $selectPlans = ["id", "name", "description"];
        if ($this->use_variants) {
            $rawVariants = DB::raw(
                "(select count(distinct p.shopify_variant_id) from plans p where p.shopify_id = plans.shopify_id and p.shopify_id is not null and p.deleted_at is null) as variants"
            );
            $selectPlans[] = $rawVariants;
        }

        if ($this->apply_on_shipping[0] === "all") {
            $this->apply_on_shipping = collect()->push(
                (object) [
                    "id" => "all",
                    "name" => "Qualquer frete",
                    "information" => "",
                ]
            );
        } else {
            $this->apply_on_shipping = Shipping::select("id", "name", "information")
                ->whereIn("id", $this->apply_on_shipping)
                ->get();
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
        $this->offer_on_plans = Plan::select($selectPlans)
            ->whereIn("id", $this->offer_on_plans)
            ->get();

        return [
            "id" => Hashids::encode($this->id),
            "description" => Str::limit($this->description, 20),
            "discount" => $this->discount,
            "type" => $this->type,
            "active_flag" => $this->active_flag,
            "use_variants" => $this->use_variants,
            "apply_on_shipping" => $this->apply_on_shipping->map(function ($shipping) {
                return [
                    "id" => $shipping->id === "all" ? "all" : Hashids::encode($shipping->id),
                    "name" => $shipping->name,
                    "information" => $shipping->information,
                ];
            }),
            "apply_on_plans" => $this->apply_on_plans->map(function ($plan) {
                return [
                    "id" => $plan->id === "all" ? "all" : Hashids::encode($plan->id),
                    "name" => $plan->name,
                    "description" => !empty($plan->variants) ? $plan->variants . " variantes" : $plan->description,
                ];
            }),
            "offer_on_plans" => $this->offer_on_plans->map(function ($plan) {
                return [
                    "id" => Hashids::encode($plan->id),
                    "name" => $plan->name,
                    "description" => !empty($plan->variants) ? $plan->variants . " variantes" : $plan->description,
                ];
            }),
        ];
    }
}
