<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\AffiliateLink;
use Modules\Core\Entities\AffiliateRequest;
use Modules\Core\Entities\Plan;
use Vinkla\Hashids\Facades\Hashids;

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
    public function createAffiliateLink(int $affiliateId, int $projectId)
    {
        try {
            $affiliateLinkModel = new AffiliateLink();
            $planModel          = new Plan();
            $plans              = $planModel->where('project_id', $projectId)->get();
            $projectHash        = Hashids::connection('affiliate')->encode($projectId);
            $userHash           = Hashids::connection('affiliate')->encode(auth()->user()->account_owner_id);
            if (!empty($projectHash) && !empty($userHash)) {
                $affiliateHash = $projectHash . $userHash;
                foreach ($plans as $plan) {
                    $affiliateLinkModel->create([
                                                    'affiliate_id'  => $affiliateId,
                                                    'plan_id'       => $plan->id,
                                                    'parameter'     => $affiliateHash,
                                                    'clicks_amount' => 0,
                                                ]);
                }

                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            Log::warning('Erro ao criar link de afiliado (AffiliateService - createAffiliateLink)');
            report($e);

            return false;
        }
    }
}
