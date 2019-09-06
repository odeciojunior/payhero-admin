<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class ShopifyIntegrationPresenter extends Presenter
{
    public function getThemeType($status)
    {

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'basic_theme';
                case 2:
                    return 'ajax_theme';
            }

            return '';
        } else {
            switch ($status) {
                case 'basic_theme':
                    return 1;
                case 'ajax_theme':
                    return 2;
            }

            return '';
        }
    }

    public function getStatus($status)
    {

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'pending';
                case 2:
                    return 'approved';
                case 3:
                    return 'disabled';
            }

            return '';
        } else {
            switch ($status) {
                case 'pending':
                    return 1;
                case 'approved':
                    return 2;
                case 'disabled':
                    return 3;
            }

            return '';
        }
    }
}
