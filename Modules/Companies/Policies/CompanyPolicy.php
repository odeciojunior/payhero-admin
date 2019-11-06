<?php

namespace Modules\Companies\Policies;

use Modules\Core\Entities\User;
use Modules\Core\Entities\Company;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
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
    public function index(User $user, Company $company)
    {
        if ($user->account_owner == $company->user_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Company $company
     * @return bool
     */
    public function edit(User $user, Company $company)
    {
        if ($user->account_owner == $company->user_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Company $company
     * @return bool
     */
    public function update(User $user, Company $company)
    {
        if ($user->account_owner == $company->user_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Company $company
     * @return bool
     */
    public function destroy(User $user, Company $company)
    {
        if ($user->account_owner == $company->user_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Company $company
     * @return bool
     */
    public function uploadDocuments(User $user, Company $company)
    {
        if ($user->account_owner == $company->user_id) {
            return true;
        } else {
            return false;
        }
    }


}
