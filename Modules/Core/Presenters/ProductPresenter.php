<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class NotazzInvoicePresenter
 * @package Modules\Core\Presenters
 */
class ProductPresenter extends Presenter
{
    /**
     * @param $currency
     * @return int|string
     */
    public function getCurrency($currency)
    {
        if (is_numeric($currency)) {
            switch ($currency) {
                case 1:
                    return 'BRL';
                case 2:
                    return 'USD';
            }

            return '';
        } else {
            switch ($currency) {
                case 'BRL':
                    return 1;
                case 'USD':
                    return 2;
            }

            return '';
        }
    }
}
