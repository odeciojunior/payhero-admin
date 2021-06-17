<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Plan;

class PlanService
{

    function getCheckoutLink(Plan $plan)
    {
        return count(
            $plan->project->domains
        ) > 0 ? 'https://checkout.' . $plan->project->domains[0]->name . '/' . $plan->code : '';
    }

    public function getPlansApplyDecoded($plans): array
    {
        $applyPlanArray = [];
        if (in_array('all', $plans)) {
            $applyPlanArray[] = 'all';
        } else {
            foreach ($plans as $value) {
                $applyPlanArray[] = hashids_decode($value);
            }
        }

        return $applyPlanArray;
    }
}
