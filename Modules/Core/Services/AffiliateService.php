<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\AffiliateLink;
use Modules\Core\Entities\AffiliateRequest;
use Modules\Core\Entities\Plan;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\PlanService;

/**
 * Class AffiliateService
 * @package Modules\Core\Services
 */
class AffiliateService
{
    /**
     * @param int $affiliateId
     * @param int $projectId
     * @return bool
     */
    public function createAffiliateLinks(int $affiliateId, int $projectId)
    {
        try {
            $affiliateLinkModel = new AffiliateLink();
            $planModel          = new Plan();
            $planService        = new PlanService();
            $plans              = $planModel->where('project_id', $projectId)->get();
            $projectHash        = Hashids::connection('affiliate')->encode($projectId);
            $affiliateHash      = Hashids::connection('affiliate')->encode($affiliateId);

            foreach ($plans as $plan) {
                $affiliateLinkModel->create([
                    'affiliate_id'  => $affiliateId,
                    'plan_id'       => $plan->id,
                    'parameter'     => $affiliateHash . Hashids::connection('affiliate')->encode($plan->id),
                    'clicks_amount' => 0,
                    'link'          => $planService->getCheckoutLink($plan),
                ]);
            }

            //criar affiliate link sem plano
            $affiliateLinkModel->create([
                'affiliate_id'  => $affiliateId,
                'plan_id'       => null,
                'parameter'     => $affiliateHash . $projectHash,
                'clicks_amount' => 0,
                'link'              => 'https://' . $plans->first()->project->domains->first()->name . '/',
            ]);

            return true;
        } catch (Exception $e) {
            Log::warning('Erro ao criar link de afiliado (AffiliateService - createAffiliateLinks)');
            report($e);

            return false;
        }
    }
}
