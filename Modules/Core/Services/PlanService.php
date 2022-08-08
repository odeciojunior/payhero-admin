<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Project;

class PlanService
{
    function getCheckoutLink(Plan $plan)
    {
        return count($plan->project->domains) > 0
            ? "https://checkout." . $plan->project->domains[0]->name . "/" . $plan->code
            : "";
    }

    public function getPlansFilter(int $projectId, string $plan)
    {
        $planModel = new Plan();

        if (!empty($plan)) {
            return $planModel
                ->with("productsPlans")
                ->where("project_id", $projectId)
                ->where("name", "like", "%" . $plan . "%")
                ->take(12)
                ->get();
        } else {
            return $planModel
                ->with("productsPlans")
                ->where("project_id", $projectId)
                ->take(12)
                ->get();
        }
    }

    public function getPlansApplyDecoded($plans): array
    {
        $applyPlanArray = [];
        if (in_array("all", $plans)) {
            $applyPlanArray[] = "all";
        } else {
            foreach ($plans as $value) {
                $applyPlanArray[] = hashids_decode($value);
            }
        }

        return $applyPlanArray;
    }

    public static function forgetCache($id)
    {
        CacheService::forget(CacheService::CHECKOUT_PARAM_PLAN, $id);
        CacheService::forget(CacheService::CHECKOUT_PARAM_PRODUCT_PLANS, $id);
        CacheService::forget(CacheService::CHECKOUT_CART_PLAN, $id);
        CacheService::forget(CacheService::SHIPPING_PLAN, $id);
        CacheService::forget(CacheService::SHIPPING_PLAN_VARIANTS, $id);
        CacheService::forgetContainsUnique(CacheService::CHECKOUT_CART_PRODUCT_PLANS, $id);
        CacheService::forgetContainsUnique(CacheService::CHECKOUT_OB_APPLY_ON_PLANS, $id);
        CacheService::forgetContainsUnique(CacheService::CHECKOUT_ONLY_DIGITAL_PRODUCTS, $id);
        CacheService::forgetContainsUnique(CacheService::SHIPPING_OB_PLANS, $id);
    }
}
