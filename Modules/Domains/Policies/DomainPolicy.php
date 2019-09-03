<?php

namespace Modules\Domains\Policies;

use App\Entities\Project;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserProject;
use Illuminate\Auth\Access\HandlesAuthorization;

class DomainPolicy
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
