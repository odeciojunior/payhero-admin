<?php

namespace Modules\OrderBump\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class OrderBumpShowResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => Hashids::encode($this->id),
            'description' => $this->description,
            'active_flag' => $this->active_flag,
            'discount' => $this->discount,
            'apply_on_plans' => $this->getAttributes()['apply_on_plans']
                ->map(function ($plan) {
                    return [
                        'id' => $plan->id === 'all' ? 'all' : Hashids::encode($plan->id),
                        'name' => $plan->name,
                        'variants' => $plan->variants,
                    ];
                }),
            'offer_plans' => $this->getAttributes()['offer_plans']->map(function ($plan) {
                return [
                    'id' => Hashids::encode($plan->id),
                    'name' => $plan->name,
                    'variants' => $plan->variants,
                ];
            }),
        ];
    }
}
