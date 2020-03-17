<?php

namespace Modules\ProjectUpsellConfig\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Core\Entities\Plan;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class PreviewUpsellResource extends Resource
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
                $applyPlanArray[] = ['id' => 'all', 'name' => 'Todos'];
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
                $plan             = $planModel->with('products')->find($value);
                $offerPlanArray[] = $plan;
            }
        }

        return [
            'id'             => Hashids::encode($this->id),
            'header'         => $this->header,
            'title'          => $this->title,
            'description'    => $this->description,
            'countdown_time' => $this->countdown_time ?? '',
            'countdown_flag' => $this->countdown_flag,
            'apply_on_plans' => $applyPlanArray,
            'offer_on_plans' => $offerPlanArray,
        ];
    }
}
