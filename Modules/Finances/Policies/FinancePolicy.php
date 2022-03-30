<?php

namespace Modules\Finances\Policies;

use Modules\Core\Entities\User;
use Modules\Core\Entities\Company;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class FinancePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param User $user
     * @param Company $company
     * @return bool
     */
    public function edit(User $user, Company $company)
    {
        if ($user->account_owner_id == $company->user_id) {
            return true;
        } else {
            return false;
        }
    }
}
