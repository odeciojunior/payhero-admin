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

        $selectPlans = ['id', 'name', 'description'];
        if ($this->use_variants) {
            $rawVariants = DB::raw('(select sum(if(p.shopify_id is not null and p.shopify_id = plans.shopify_id, 1, 0)) from plans p) as variants');
            $selectPlans[] = $rawVariants;
        }

        if ($this->apply_on_shipping[0] === 'all') {
            $this->apply_on_shipping = collect()->push((object)[
                'id' => 'all',
                'name' => 'Qualquer frete',
                'information' => '',
            ]);
        } else {
            $this->apply_on_shipping = Shipping::select('id', 'name', 'information')
                ->whereIn('id', $this->apply_on_shipping)
                ->get();
        }

        if ($this->apply_on_plans[0] === 'all') {
            $this->apply_on_plans = collect()->push((object)[
                'id' => 'all',
                'name' => 'Qualquer plano',
                'description' => '',
                'variants' => 0,
            ]);
        } else {
            $this->apply_on_plans = Plan::select($selectPlans)
                ->whereIn('id', $this->apply_on_plans)
                ->get();
        }
        $this->offer_on_plans = Plan::select($selectPlans)
            ->whereIn('id', $this->offer_on_plans)
            ->get();

        return [
            'id' => Hashids::encode($this->id),
            'description' => Str::limit($this->description, 20),
            'discount' => $this->discount,
            'active_flag' => $this->active_flag,
            'use_variants' => $this->use_variants,
            'apply_on_shipping' => $this->apply_on_shipping,
            'apply_on_plans' => $this->apply_on_plans,
            'offer_on_plans' => $this->offer_on_plans,
        ];
    }
}
