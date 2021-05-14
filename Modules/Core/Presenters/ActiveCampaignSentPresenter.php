<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class ActiveCampaignSentPresenter extends Presenter
{

    public function getSentStatus($status) {

        if(is_numeric($status)){
            switch ($status) {
                case 1:
                    return 'error';
                case 2:
                    return 'success';
                case 3: 
                    return 'canceled';
            }
            return '';
        }
        else{
            switch ($status) {
                case 'error':
                    return 1;
                case 'success':
                    return 2;
                case 'canceled':
                    return 3;
            }
            return '';
        }

    }

}
