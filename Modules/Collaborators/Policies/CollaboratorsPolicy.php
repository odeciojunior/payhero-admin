<?php

namespace Modules\Collaborators\Policies;

use Modules\Core\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollaboratorsPolicy
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


}
