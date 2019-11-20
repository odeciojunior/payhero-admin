<?php

namespace Modules\Profile\Policies;

use Modules\Core\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
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
     * @param User $model
     * @return bool
     */
    public function changePassword(User $user, User $model)
    {
        if ($user->id == $model->id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function uploadDocuments(User $user, User $model)
    {
        if ($user->account_owner_id == $model->account_owner_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function edit(User $user, User $model)
    {
        if ($user->account_owner_id == $model->account_owner_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function update(User $user, User $model)
    {
        if ($user->id == $model->id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function view(User $user, User $model)
    {
        if ($user->id == $model->id) {
            return true;
        } else {
            return false;
        }
    }
}


