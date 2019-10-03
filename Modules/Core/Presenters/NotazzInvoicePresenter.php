<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class NotazzInvoicePresenter
 * @package Modules\Core\Presenters
 */
class NotazzInvoicePresenter extends Presenter
{
    /**
     * @param $status
     * @return int|string
     */
    public function getStatus($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'pending';
                case 2:
                    return 'send';
                case 3:
                    return 'completed';
                case 4:
                    return 'error';
                case 5:
                    return 'in_process';
                case 6:
                    return 'error_max_attempts';
            }

            return '';
        } else {
            switch ($status) {
                case 'pending':
                    return 1;
                case 'send':
                    return 2;
                case 'completed':
                    return 3;
                case 'error':
                    return 4;
                case 'in_process':
                    return 5;
                case 'error_max_attempts':
                    return 6;
            }

            return '';
        }
    }

    /**
     * @param $type
     * @return int|string
     */
    public function getInvoiceType($type)
    {
        if (is_numeric($type)) {
            switch ($type) {
                case 1:
                    return 'service';
                case 2:
                    return 'product';
            }

            return '';
        } else {
            switch ($type) {
                case 'service':
                    return 1;
                case 'product':
                    return 2;
            }

            return '';
        }
    }
}
