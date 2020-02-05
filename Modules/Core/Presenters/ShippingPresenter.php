<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class ShippingPresenter extends Presenter
{
    public function getStatus($status)
    {

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'active';
                case 0:
                    return 'disabled';
            }

            return '';
        } else {
            switch ($status) {
                case 'active':
                    return 1;
                case 'disabled':
                    return 0;
            }

            return '';
        }
    }

    public function getPreSelectedStatus($status)
    {

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'yes';
                case 0:
                    return 'no';
            }

            return '';
        } else {
            switch ($status) {
                case 'yes':
                    return 1;
                case 'no':
                    return 0;
            }

            return '';
        }
    }

    public function getTypeEnum($type)
    {
        if (is_numeric($type)) {

            switch ($type) {
                case 1:
                    return "static";
                case 2:
                    return "sedex";
                case 3:
                    return "pac";
            }
        } else {
            switch ($type) {
                case "static":
                    return 1;
                case "sedex":
                    return 2;
                case "pac":
                    return 3;
            }
        }

        return '';
    }

    /**
     * @param $type
     * @return string
     */
    public function getTransalatedType(int $type)
    {
        return $type == 1 ? 'Estático' : ($this->type == 2 ? 'SEDEX - Calculado automáticamente' : 'PAC - Calculado automáticamente');
    }
}
