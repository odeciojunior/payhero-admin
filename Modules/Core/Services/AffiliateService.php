<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\AffiliateLink;
use Modules\Core\Entities\AffiliateRequest;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;

/**
 * Class AffiliateService
 * @package Modules\Core\Services
 */
class AffiliateService
{
    /**
     * @param int $affiliateId
     * @param int $projectId
     */
    public function createAffiliateLink(int $affiliateId, int $projectId)
    {
        $affiliateLinkModel = new AffiliateLink();
        $projectModel       = new Project();
        //        $domainModel        = new Domain();

        $project = $projectModel->with('plans')->find($projectId);
        //        $domain  = $domainModel->where('project_id', $projectId)->first();
        foreach ($project->plans as $plan) {
            $affiliateLinkModel->create([
                                            'affiliate_id' => $affiliateId,
                                            'plan_id'      => $plan->id,
                                        ]);
        }
    }
}
