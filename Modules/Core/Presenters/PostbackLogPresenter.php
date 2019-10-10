<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class PostbackLogPresenter extends Presenter
{
    /**
     * @return string
     */
    public function getOrigin($status)
    {

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'ebanx';
                case 2:
                    return 'pagarme';
                case 3:
                    return 'shopify';
                case 4:
                    return 'mercado-pago';
            }

            return '';
        } else {
            switch ($status) {
                case 'ebanx':
                    return 1;
                case 'pagarme':
                    return 2;
                case 'shopify':
                    return 3;
                case 'mercado-pago':
                    return 4;
            }

            return '';
        }
    }
}
