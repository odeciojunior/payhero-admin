<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class ProductPlanSalePresenter extends Presenter
{
    public function getTrackingStatusEnum($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return "posted";
                case 2:
                    return "dispatched";
                case 3:
                    return "delivered";
                case 4:
                    return "out_for_delivery";
                case 5:
                    return "exception";
            }

            return "";
        } else {
            switch ($status) {
                case "posted":
                    return 1;
                case "dispatched":
                    return 2;
                case "delivered":
                    return 3;
                case "out_for_delivery":
                    return 4;
                case "exception":
                    return 5;
            }

            return "";
        }
    }
}
