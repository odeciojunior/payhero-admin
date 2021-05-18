<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class AffiliatePresenter extends Presenter
{
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
}
