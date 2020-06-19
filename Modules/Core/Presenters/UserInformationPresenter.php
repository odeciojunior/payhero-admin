<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class UserInformationPresenter
 * @package Modules\Core\Presenters
 */
class UserInformationPresenter extends Presenter
{
    /**
     * @param $status
     * @return int|string
     */
    public function getMaritalStatus($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'married';
                case 2:
                    return 'single';
                case 3:
                    return 'divorced';
                case 4:
                    return 'separated';
                case 5:
                    return 'widowed';
            }
            
            return '';
        } else {
            switch ($status) {
                case 'married':
                    return 1;
                case 'single':
                    return 2;
                case 'divorced':
                    return 3;
                case 'separated':
                    return 4;
                case 'widowed':
                    return 5;
            }

            return '';
        }
    }

    /**
     * @param $type
     * @return int|string
     */
    public function getDocumentType($type)
    {
        if (is_numeric($type)) {
            switch ($type) {
                case 1:
                    return 'id_card';
                case 2:
                    return 'driver_license';
                case 3:
                    return 'rne';
            }

            return '';
        } else {
            switch ($type) {
                case 'id_card':
                    return 1;
                case 'driver_license':
                    return 2;
                case 'rne':
                    return 3;
            }

            return '';
        }
    }

}