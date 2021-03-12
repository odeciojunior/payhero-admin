<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class BlockReasonPresenter extends Presenter
{
    public function getReasonEnum($reasonEnum)
    {
        if (is_numeric($reasonEnum)) {
            switch ($reasonEnum) {
                case 1:
                    return 'in_dispute';
                case 2:
                    return 'without_tracking';
                case 3:
                    return 'no_tracking_info';
                case 4:
                    return 'unknown_carrier';
                case 5:
                    return 'posted_before_sale';
                case 6:
                    return 'duplicated';
                case 7:
                    return 'others';
            }

            return '';
        } else {
            switch ($reasonEnum) {
                case 'in_dispute':
                    return 1;
                case 'without_tracking':
                    return 2;
                case 'no_tracking_info':
                    return 3;
                case 'unknown_carrier':
                    return 4;
                case 'posted_before_sale':
                    return 5;
                case 'duplicated':
                    return 6;
                case 'others':
                    return 7;
            }

            return '';
        }
    }
}