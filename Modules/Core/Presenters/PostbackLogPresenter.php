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
                    return 'shopify-products';
                case 4:
                    return 'mercado-pago';
                case 5:
                    return 'shopify-tracking';
                case 6:
                    return 'notazz';
            }

            return '';
        } else {
            switch ($status) {
                case 'ebanx':
                    return 1;
                case 'pagarme':
                    return 2;
                case 'shopify-products':
                    return 3;
                case 'mercado-pago':
                    return 4;
                case 'shopify-tracking':
                    return 5;
                case 'notazz':
                    return 6;
            }

            return '';
        }
    }
}
