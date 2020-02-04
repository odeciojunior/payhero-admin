<?php


namespace Modules\Core\Presenters;

/**
 * Class UserProjectsPresenter
 * @package Modules\Core\Presenters
 */
class UserProjectsPresenter
{
    /**
     * @param $type
     * @return int|string
     */
    public function getTypeEnum($type)
    {
        if (is_numeric($type)) {
            switch ($type) {
                case 1:
                    return 'producer';
                case 2:
                    return 'partner';

            }

            return '';
        } else {
            switch ($type) {
                case 'producer':
                    return 1;
                case 'partner':
                    return 2;
            }

            return '';
        }
    }

    /**
     * @param $status
     * @return int|string
     */
    public function getStatusFlag($status)
    {
        if (is_numeric($status)) {

            switch ($status) {
                case 1:
                    return "active";
                case 0:
                    return "inactive";
            }
        } else {
            switch ($status) {
                case "active":
                case "ativo":
                    return 1;
                case "inactive":
                    return 0;
            }
        }

        return '';

    }
}
