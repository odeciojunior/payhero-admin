<?php

declare(strict_types=1);

namespace Modules\Core\Observers;

use Modules\Core\Entities\Company;
use Modules\Core\Services\AccountApprovedService;

/**
 * Class CompanyObserver
 * @package Modules\Core\Observers
 */
class CompanyObserver
{
    /**
     * Handle the user "updated" event.
     *
     * @param  Company  $company
     * @return void
     */
    public function updated(Company $company)
    {
        $user = $company->user;

        $accountApprovedService = new AccountApprovedService();
        $accountApprovedService->checkAccountIsApproved($user);

    }
}
