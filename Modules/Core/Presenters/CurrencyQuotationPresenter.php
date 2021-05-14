<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class CurrencyQuotationPresenter
 * @package Modules\Core\Presenters
 */
class CurrencyQuotationPresenter extends Presenter
{
    /**
     * @param $type
     * @return int|string
     */
    public function getCurrencyType($type)
    {
        if (is_numeric($type)) {
            switch ($type) {
                case 1:
                    return 'BRL';
                case 2:
                    return 'USD';
            }

            return '';
        } else {
            switch ($type) {
                case 'BRL':
                    return 1;
                case 'USD':
                    return 2;
            }

            return '';
        }
    }
}
