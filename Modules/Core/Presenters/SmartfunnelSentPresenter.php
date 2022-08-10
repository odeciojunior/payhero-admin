<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class SmartfunnelSentPresenter extends Presenter
{
    public function getEvent($event)
    {
        if (is_numeric($event)) {
            switch ($event) {
                case 1:
                    return "billet_pending";
                case 2:
                    return "billet_paid";
                case 3:
                    return "credit_card_paid";
            }
            return "";
        } else {
            switch ($event) {
                case "billet_pending":
                    return 1;
                case "billet_paid":
                    return 2;
                case "credit_card_paid":
                    return 3;
            }
            return "";
        }
    }

    public function getEvents()
    {
        return [
            1 => "billet_pending",
            2 => "billet_paid",
            3 => "credit_card_paid",
        ];
    }

    public function getSentStatus($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return "error";
                case 2:
                    return "success";
                case 3:
                    return "canceled";
            }
            return "";
        } else {
            switch ($status) {
                case "error":
                    return 1;
                case "success":
                    return 2;
                case "canceled":
                    return 3;
            }
            return "";
        }
    }
}
