<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class NotazzInvoicePresenter
 * @package Modules\Core\Presenters
 */
class ProductPlanPresenter extends Presenter
{
    /**
     * @param $status
     * @return int|string
     */
    public function getCurrency($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'BRL';
                case 2:
                    return 'USD';
            }

            return '';
        } else {
            switch ($status) {
                case 'BRL':
                    return 1;
                case 'USD':
                    return 2;
            }

            return '';
        }
    }
}
