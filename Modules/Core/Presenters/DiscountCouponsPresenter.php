<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class DiscountCouponsPresenter extends Presenter
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
}
