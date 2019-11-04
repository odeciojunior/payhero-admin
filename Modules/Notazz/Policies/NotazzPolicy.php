<?php

namespace Modules\Notazz\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserProject;

class NotazzPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param NotazzIntegration $notazzIntegration
     * @return bool
     */
    public function show(User $user, NotazzIntegration $notazzIntegration)
    {

        $notazzIntegration->load('project');

        $userProject = UserProject::where('user_id', $user->id)
                                  ->where('project_id', $notazzIntegration->project->id)
                                  ->first();
        if ($userProject) {
            return true;
        } else {
            return false;
        }
    }
}
