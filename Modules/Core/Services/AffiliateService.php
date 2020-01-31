<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\AffiliateRequest;

/**
 * Class AffiliateService
 * @package Modules\Core\Services
 */
class AffiliateService
{
    /**
     * @param int $projectId
     * @param string $type
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function createAffiliate(int $projectId, string $type)
    {
        if ($type == 'confirm') {
            $affiliateModel = new Affiliate();
        } else {
            $affiliateRequestModel = new AffiliateRequest();
            $affiliateRequestModel->create([
                                               'user_id'    => auth()->account_owner_id,
                                               'project_id' => $projectId,
                                               'status'     => $affiliateRequestModel->present()->getStatus('pending'),
                                           ]);
        }
    }
}
