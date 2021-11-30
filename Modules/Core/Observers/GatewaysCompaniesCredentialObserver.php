<?php

declare(strict_types=1);

namespace Modules\Core\Observers;

use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Services\AccountApprovedService;

/**
 * Class GatewaysCompaniesCredentialObserver
 * @package App\Observers
 */
class GatewaysCompaniesCredentialObserver
{
    /**
     * Handle the user "updated" event.
     *
     * @param GatewaysCompaniesCredential $gatewayCredential
     * @return void
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function updated(GatewaysCompaniesCredential $gatewayCredential)
    {
        if(!isset($gatewayCredential->company))
            return null;

        $user = $gatewayCredential->company->user;

        $accountApprovedService = new AccountApprovedService();
        $accountApprovedService->checkAccountIsApproved($user);

    }
}
