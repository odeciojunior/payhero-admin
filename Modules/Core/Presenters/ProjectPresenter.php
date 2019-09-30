<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;

class ProjectPresenter extends Presenter
{
    /**
     * @return mixed
     */
    public function getProjects()
    {
        $projectModel     = new Project();
        $userProjectModel = new UserProject();

        $userProjects = $userProjectModel->where('user_id', auth()->user()->id)->pluck('project_id');

        return $projectModel->whereIn('id', $userProjects)->get();
    }

    public function getStatus($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'approved';
            }

            return '';
        } else {
            switch ($status) {
                case 'approved':
                    return 1;
            }

            return '';
        }
    }
}
