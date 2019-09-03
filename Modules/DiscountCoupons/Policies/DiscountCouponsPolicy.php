<?php

namespace Modules\DiscountCoupons\Policies;

use Modules\Core\Entities\UserProject;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiscountCouponsPolicy
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
     * @param Project $project
     * @return bool
     */
    public function show(User $user, Project $project)
    {
        $userProject = UserProject::where('user', $user->id)
                                  ->where('project', $project->id)
                                  ->first();
        if ($userProject) {
            return true;
        } else {
            return false;
        }
    }
}
