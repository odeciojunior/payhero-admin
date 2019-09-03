<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class WithdrawalPresenter extends Presenter
{
    public function getStatus($status)
    {

        if(is_numeric($status)){
            switch ($status) {
                case 1:
                    return 'approved';
                case 2:
                    return 'pending';
                case 3:
                    return 'refused';
                case 4:
                    return 'charge_back';
                case 5:
                    return 'canceled';
                case 6:
                    return 'in_proccess';
                case 10:
                    return 'system_error';
            }
            return '';
        }
        else{
            switch ($status) {
                case 'approved':
                    return 1;
                case 'pending':
                    return 2;
                case 'refused':
                    return 3;
                case 'charge_back':
                    return 4;
                case 'canceled':
                    return 5;
                case 'in_proccess':
                    return 6;
                case 'system_error':
                    return 10;
            }
            return '';
        }

    }

}
