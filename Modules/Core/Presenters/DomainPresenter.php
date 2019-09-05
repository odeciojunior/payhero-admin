<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class DomainPresenter extends Presenter
{
    public function getStatus($status)
    {

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'accepted';
                case 2:
                    return 'pending';
                case 3:
                    return 'expired';
            }

            return '';
        } else {
            switch ($status) {
                case 'accepted':
                    return 1;
                case 'pending':
                    return 2;
                case 'expired':
                    return 3;
            }

            return '';
        }
    }
}
