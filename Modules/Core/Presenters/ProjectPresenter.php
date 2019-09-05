<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;

class ProjectPresenter extends Presenter
{
    public function getProjects()
    {
        $projectModel     = new Project();
        $userProjectModel = new UserProject();

        $userProjects = $userProjectModel->where('user_id', auth()->user()->id)->pluck('project_id');

        return $projectModel->whereIn('id', $userProjects)->get();
    }
}
