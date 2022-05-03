<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class BlockReasonPresenter extends Presenter
{
    public function getReasonEnum($reasonEnum)
    {
        $reasonArray = [
            1 => 'in_dispute',
            2 => 'without_tracking',
            3 => 'no_tracking_info',
            4 => 'unknown_carrier',
            5 => 'posted_before_sale',
            6 => 'duplicated',
            7 => 'others',
            8 => 'ticket',
        ];
        return (is_numeric($reasonEnum) ? $reasonArray[$reasonEnum] : array_search($reasonEnum, $reasonArray)) ?? '';
    }
}
