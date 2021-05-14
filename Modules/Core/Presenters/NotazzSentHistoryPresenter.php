<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class NotazzInvoicePresenter
 * @package Modules\Core\Presenters
 */
class NotazzSentHistoryPresenter extends Presenter
{
    /**
     * @param $type
     * @return int|string
     */
    public function getType($type)
    {
        if (is_numeric($type)) {
            switch ($type) {
                case 1:
                    return 'sent';
                case 2:
                    return 'update';
                case 3:
                    return 'consult';
                case 4:
                    return 'delete';
            }

            return '';
        } else {
            switch ($type) {
                case 'sent':
                    return 1;
                case 'update':
                    return 2;
                case 'consult':
                    return 3;
                case 'delete':
                    return 4;
            }

            return '';
        }
    }
}
