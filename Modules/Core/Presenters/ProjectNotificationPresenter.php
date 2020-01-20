<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class ProjectNotificationPresenter extends Presenter
{

    public function getEventEnum($event) {

        if(is_numeric($event)){
            switch ($event) {
                case 1:
                    return 'billet_generated';
                case 2:
                    return 'billet_paid';
                case 3: 
                    return 'credit_paid';
                case 4: 
                    return 'abandoned_cart';
                case 5: 
                    return 'billet_winning';
                case 6:
                    return 'tracking';
            }
            return '';
        }
        else{
            switch ($event) {
                case 'billet_generated':
                    return 1;
                case 'billet_paid':
                    return 2;
                case 'credit_paid':
                    return 3;
                case 'abandoned_cart': 
                    return 4;
                case 'billet_winning': 
                    return 5;
                case 'tracking':
                    return 6;
            }
            return '';
        }

    }

    public function getTypeEnum($event) {

        if(is_numeric($event)){
            switch ($event) {
                case 1:
                    return 'email';
                case 2:
                    return 'sms';
            }
            return '';
        }
        else{
            switch ($event) {
                case 'email':
                    return 1;
                case 'sms':
                    return 2;
            }
            return '';
        }

    }

}
