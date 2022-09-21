<?php

namespace Modules\OrderBump\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class OrderBumpShowResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "description" => $this->description,
            "active_flag" => $this->active_flag,
            "use_variants" => $this->use_variants,
            "discount" => $this->discount,
            "type" => $this->type,
            "apply_on_shipping" => $this->getAttributes()["apply_on_shipping"]->map(function ($shipping) {
                return [
                    "id" => $shipping->id === "all" ? "all" : Hashids::encode($shipping->id),
                    "name" => $shipping->name,
                    "information" => $shipping->information,
                ];
            }),
            "apply_on_plans" => $this->getAttributes()["apply_on_plans"]->map(function ($plan) {
                return [
                    "id" => $plan->id === "all" ? "all" : Hashids::encode($plan->id),
                    "name" => $plan->name,
                    "description" => $plan->description,
                ];
            }),
            "offer_plans" => $this->getAttributes()["offer_plans"]->map(function ($plan) {
                return [
                    "id" => Hashids::encode($plan->id),
                    "name" => $plan->name,
                    "description" => $plan->description,
                ];
            }),
        ];
    }
}
