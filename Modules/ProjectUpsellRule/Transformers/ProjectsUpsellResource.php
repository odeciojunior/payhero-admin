<?php

namespace Modules\ProjectUpsellRule\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Entities\Plan;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ProjectsUpsellResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        $applyPlanArray = [];
        $offerPlanArray = [];
        $planModel      = new Plan();
        $rawVariants = DB::raw('(select sum(if(p.shopify_id is not null and p.shopify_id = plans.shopify_id, 1, 0)) from plans p) as variants');

        if (!empty($this->apply_on_plans)) {
            $applyPlanDecoded = json_decode($this->apply_on_plans);
            if (in_array('all', $applyPlanDecoded)) {
                $applyPlanArray[] = ['id' => 'all', 'name' => 'Qualquer plano', 'description' => ''];
            } else {
                foreach ($applyPlanDecoded as $key => $value) {
                    $plan = $planModel->select('plans.*', $rawVariants)->find($value);
                    if (!empty($plan)) {
                        $applyPlanArray[] = [
                            'id' => Hashids::encode($plan->id),
                            'name' => $plan->name,
                            'description' => $plan->variants ? $plan->variants . ' variantes' : $plan->description,
                        ];
                    }
                }
            }
        }
        if (!empty($this->offer_on_plans)) {
            $offerPlanDecoded = json_decode($this->offer_on_plans);
            foreach ($offerPlanDecoded as $key => $value) {
                $plan = $planModel->select('plans.*', $rawVariants)->find($value);
                if (!empty($plan)) {
                    $offerPlanArray[] = [
                        'id' => Hashids::encode($plan->id),
                        'name' => $plan->name,
                        'description' => $plan->variants ? $plan->variants . ' variantes' : $plan->description,
                    ];
                }
            }
        }

        return [
            'id'             => Hashids::encode($this->id),
            'description'    => Str::limit($this->description, 20),
            'discount'       => $this->discount,
            'active_flag'    => $this->active_flag,
            'apply_on_plans' => $applyPlanArray,
            'offer_on_plans' => $offerPlanArray,
        ];
    }
}
