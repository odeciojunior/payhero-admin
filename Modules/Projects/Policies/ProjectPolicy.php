<?php

namespace Modules\Projects\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Affiliate;

class ProjectPolicy
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
     * @param Project $project
     * @return bool
     */
    public function index(User $user, Project $project)
    {
        $userProject = UserProject::where('user_id', $user->account_owner_id)
                                  ->where('project_id', $project->id)
                                  ->first();
        if ($userProject) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function show(User $user, Project $project)
    {
        $userProject = UserProject::where('user_id', $user->account_owner_id)
                                  ->where('project_id', $project->id)
                                  ->first();

        $affiliateProject = Affiliate::where('user_id', $user->account_owner_id)
                                      ->where('project_id', $project->id)
                                      ->first();

        if ($userProject || $affiliateProject) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function edit(User $user, Project $project, $checkAffiliate = false)
    {
        $userProject = UserProject::where('user_id', $user->account_owner_id)
                                  ->where('project_id', $project->id)
                                  ->first();
        if($checkAffiliate) {
            $affiliate = Affiliate::where('user_id', $user->account_owner_id)
                                  ->where('project_id', $project->id)
                                  ->first();
        }
        if ($userProject || $affiliate) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function update(User $user, Project $project)
    {
        $userProject = UserProject::where('user_id', $user->account_owner_id)
                                  ->where('project_id', $project->id)
                                  ->first();
        if ($userProject) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function destroy(User $user, Project $project)
    {
        $userProject = UserProject::where('user_id', $user->account_owner_id)
                                  ->where('project_id', $project->id)
                                  ->first();
        if ($userProject) {
            return true;
        } else {
            return false;
        }
    }
}
