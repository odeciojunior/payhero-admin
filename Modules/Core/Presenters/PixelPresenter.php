<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class PixelPresenter extends Presenter
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

    public function getPlatformEnum($platform)
    {

        if (is_numeric($platform)) {
            switch ($platform) {
                case 1:
                    return 'facebook';
                case 2:
                    return 'google_adwords';
                case 3:
                    return 'google_analytics';
                case 4:
                    return 'taboola';
                case 5:
                    return 'outbrain';
            }

            return '';
        } else {
            switch ($platform) {
                case 'facebook':
                    return 1;
                case 'google_adwords':
                    return 2;
                case 'google_analytics':
                    return 3;
                case 'taboola':
                    return 4;
                case 'outbrain':
                    return 5;
            }
            return '';
        }
    }

}
