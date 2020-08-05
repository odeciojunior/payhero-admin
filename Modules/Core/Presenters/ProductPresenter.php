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

    /**
     * @param $type
     * @return int|string
     */
    public function getType($type)
    {
        if (is_numeric($type)) {
            switch ($type) {
                case 1:
                    return 'physical';
                case 2:
                    return 'digital';
            }

            return '';
        } else {
            switch ($type) {
                case 'physical':
                    return 1;
                case 'digital':
                    return 2;
            }

            return '';
        }
    }
}
