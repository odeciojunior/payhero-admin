<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class AffiliateRequestPresenter extends Presenter
{
    public function getStatus($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return "pending";
                case 3:
                    return "approved";
                case 4:
                    return "refused";
            }

            return "";
        } else {
            switch ($status) {
                case "pending":
                    return 1;
                case "approved":
                    return 3;
                case "refused":
                    return 4;
            }

            return "";
        }
    }
}
