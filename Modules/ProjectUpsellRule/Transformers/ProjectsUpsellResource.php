<?php

namespace Modules\ProjectUpsellRule\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
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
        if (!empty($this->apply_on_plans)) {
            $applyPlanDecoded = json_decode($this->apply_on_plans);
            if (in_array('all', $applyPlanDecoded)) {
                $applyPlanArray[] = ['id' => 'all', 'name' => 'Qualquer plano'];
            } else {
                foreach ($applyPlanDecoded as $key => $value) {
                    $plan             = $planModel->find($value);
                    $applyPlanArray[] = ['id' => Hashids::encode($plan->id), 'name' => $plan->name];
                }
            }
        }
        if (!empty($this->offer_on_plans)) {
            $offerPlanDecoded = json_decode($this->offer_on_plans);
            foreach ($offerPlanDecoded as $key => $value) {
                $plan             = $planModel->find($value);
                $offerPlanArray[] = ['id' => Hashids::encode($plan->id), 'name' => $plan->name];
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
