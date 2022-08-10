<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class BlockReasonSalePresenter extends Presenter
{
    public function getStatus($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return "blocked";
                case 2:
                    return "unlocked";
            }

            return "";
        } else {
            switch ($status) {
                case "blocked":
                    return 1;
                case "unlocked":
                    return 2;
            }

            return "";
        }
    }
}
