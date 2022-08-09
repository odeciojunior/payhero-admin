<?php

namespace Modules\ProjectReviews\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Entities\Plan;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectReviewsResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        $applyPlanArray = [];
        $planModel = new Plan();

        if (!empty($this->apply_on_plans)) {
            $applyPlanDecoded = json_decode($this->apply_on_plans);
            if (in_array("all", $applyPlanDecoded)) {
                $applyPlanArray[] = ["id" => "all", "name" => "Qualquer plano", "description" => ""];
            } else {
                foreach ($applyPlanDecoded as $key => $value) {
                    $plan = $planModel->find($value);
                    if (!empty($plan)) {
                        $applyPlanArray[] = [
                            "id" => Hashids::encode($plan->id),
                            "name" => $plan->name,
                            "description" => $plan->description,
                        ];
                    }
                }
            }
        }

        return [
            "id" => Hashids::encode($this->id),
            "name" => $this->name,
            "description" => $this->description,
            "photo" => $this->photo,
            "stars" => $this->stars,
            "active_flag" => $this->active_flag,
            "apply_on_plans" => $applyPlanArray,
        ];
    }
}
