<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Core\Entities\Sale;

class ReportanaIntegrationPresenter extends Presenter
{
    private array $events = [
        1 => "billet_pending",
        2 => "billet_paid",
        3 => "credit_card_paid",
        4 => "credit_card_refused",
        5 => "abandoned_cart",
        6 => "pix_pending",
        7 => "pix_paid",
        8 => "billet_expired",
        9 => "pix_expired",
    ];

    private array $sentStatus = [
        1 => "error",
        2 => "success",
        3 => "canceled",
    ];

    public function getEvent($event)
    {
        return (is_numeric($event) ? $this->events[$event] : array_search($event, $this->events)) ?? "";
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function getSentStatus($status)
    {
        return (is_numeric($status) ? $this->sentStatus[$status] : array_search($status, $this->sentStatus)) ?? "";
    }

    public static function getSearchEvent(int $paymentMethod, int $status)
    {
        switch ($paymentMethod) {
            case Sale::CREDIT_CARD_PAYMENT:
                if ($status == Sale::STATUS_APPROVED) {
                    return "credit_card_paid";
                }
                if (in_array($status, [Sale::STATUS_REFUSED, Sale::STATUS_CANCELED_ANTIFRAUD])) {
                    return "credit_card_refused";
                }
            case Sale::BILLET_PAYMENT:
                if ($status == Sale::STATUS_APPROVED) {
                    return "billet_paid";
                }
                if ($status == Sale::STATUS_PENDING) {
                    return "billet_pending";
                }
                if ($status == Sale::STATUS_CANCELED) {
                    return "billet_expired";
                }
            case Sale::PIX_PAYMENT:
                if ($status == Sale::STATUS_APPROVED) {
                    return "pix_paid";
                }
                if ($status == Sale::STATUS_PENDING) {
                    return "pix_pending";
                }
                if ($status == Sale::STATUS_CANCELED) {
                    return "pix_expired";
                }
        }
    }
}
