<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class TransactionPresenter extends Presenter
{
    public function getType($type)
    {

        if (is_numeric($type)) {
            switch ($type) {
                case 1:
                    return 'cloudfox';
                case 2:
                    return 'producer';
                case 3:
                    return 'invite';
                case 4:
                    return 'affiliate';
                case 5:
                    return 'convertaX';
            }
            return '';
        } else {
            switch ($type) {
                case 'cloudfox':
                    return 1;
                case 'producer':
                    return 2;
                case 'invite':
                    return 3;
                case 'affiliate':
                    return 4;
                case 'convertaX':
                    return 5;
            }
            return '';
        }
    }
}
