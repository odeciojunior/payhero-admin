<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Plan;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\UserProject;
use Modules\Plans\Transformers\PlansSelectResource;

/**
 * Class ProjectService
 * @package Modules\Core\Services
 */
class PlanService
{
    /**
     * @return AnonymousResourceCollection
     */
    public function getUserPlans()
    {
        $userProjectModel = new UserProject();
        $planModel        = new Plan();
        $userProjects     = $userProjectModel->where('user_id', auth()->user()->account_owner_id)->pluck('project_id');
        $plans            = $planModel->where('project_id', $userProjects);

        return PlansSelectResource::collection($plans->get());
    }
}
