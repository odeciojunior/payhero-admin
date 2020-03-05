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

        $userProjects = $userProjectModel->where('user_id', auth()->user()->account_owner_id)->pluck('project_id');

        return $projectModel->whereIn('id', $userProjects)->get();
    }

    /**
     * @param $status
     * @return int|string
     */
    public function getStatus($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'active';
                case 2:
                    return 'disabled';
            }

            return '';
        } else {
            switch ($status) {
                case 'active':
                    return 1;
                case 'disabled':
                    return 2;
            }

            return '';
        }
    }

    /**
     * @param $currency
     * @return int|string
     */
    public function getCurrencyCost($currency)
    {
        if (is_numeric($currency)) {
            switch ($currency) {
                case 1:
                    return 'BRL';
                case 2:
                    return 'USD';
            }

            return '';
        } else {
            switch ($currency) {
                case 'BRL':
                    return 1;
                case 'USD':
                    return 2;
            }

            return '';
        }
    }

    /**
     * @param $currency
     * @return int|string
     */
    public function getCommissionTypeEnum($commissionType)
    {
        if (is_numeric($commissionType)) {
            switch ($commissionType) {
                case 1:
                    return 'first_click';
                case 2:
                    return 'last_click';
            }

            return '';
        } else {
            switch ($commissionType) {
                case 'first_click':
                    return 1;
                case 'last_click':
                    return 2;
            }

            return '';
        }
    }
}
