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

            }

            return '';
        } else {
            switch ($type) {
                case 'producer':
                    return 1;
            }

            return '';
        }
    }

    public function getTypeFlag($status)
    {
        if (is_numeric($status)) {

            switch ($status) {
                case 1:
                    return "active";
                case 0:
                    return "disabled";
            }
        } else {
            switch ($status) {
                case "active":
                case "ativo":
                    return 1;
                case "disabled":
                    return 0;
            }
        }

    }
}
