<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class ActiveCampaignEventPresenter extends Presenter
{

    public function getEvent($event) {

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
                    return 'credit_refused';
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
                case 'credit_refused': 
                    return 5;
            }
            return '';
        }

    }

    public function getEvents() {
        return [
            1 => 'billet_generated',
            2 => 'billet_paid',
            3 => 'credit_paid',
            4 => 'abandoned_cart',
            5 => 'credit_refused',
        ];
    }

}
