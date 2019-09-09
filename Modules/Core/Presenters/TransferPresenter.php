<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class TransferPresenter extends Presenter
{
    public function getTypeEnum($status)
    {

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'in';
                case 2:
                    return 'out';
            }

            return '';
        } else {
            switch ($status) {
                case 'in':
                    return 1;
                case 'out':
                    return 2;
            }

            return '';
        }
    }
}
