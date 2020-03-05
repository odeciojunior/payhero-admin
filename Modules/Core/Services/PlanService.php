<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Plan;

class PlanService {

    function getCheckoutLink(Plan $plan) {

        return 'https://checkout.' . $plan->project->domains[0]->name . '/' . $plan->code;
    }
}