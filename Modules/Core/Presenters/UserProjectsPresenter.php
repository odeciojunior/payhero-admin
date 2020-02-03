<?php


namespace Modules\Core\Presenters;


class UserProjectsPresenter
{
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

    public function getStatusEnum($status)
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

    }
}
