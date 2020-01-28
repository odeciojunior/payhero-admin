<?php

namespace Modules\Core\Policies;

use Modules\Core\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsersPolicy
{
    use HandlesAuthorization;

    /**
     * @return bool
     */
    public function show(User $user, User $collaborator)
    {
        if ($user->id == $collaborator->account_owner_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function update(User $user, User $collaborator)
    {
        if ($user->id == $collaborator->account_owner_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function destroy(User $user, User $collaborator)
    {
        if ($user->id == $collaborator->account_owner_id) {
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
    public function view(User $user, User $model)
    {
        if ($user->id == $model->id) {
            return true;
        } else {
            return false;
        }
    }
}
