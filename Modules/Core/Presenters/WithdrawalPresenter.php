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
                    return 'pending';
                case 2:
                    return 'approved'; 
                case 3: 
                    return 'transfered';
                case 4:
                    return 'refused';
            }
            return '';
        }
        else{
            switch ($status) {
                case 'pending':
                    return 1;
                case 'approved':
                    return 2;
                case 'transfered':
                    return 3;
                case 'refused':
                    return 4;
            }
            return '';
        }

    }

}
