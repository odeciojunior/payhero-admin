<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class ReportanaIntegrationPresenter extends Presenter
{
    private array $events = [
        1 => 'billet_pending',
        2 => 'billet_paid',
        3 => 'credit_card_paid',
        4 => 'credit_card_refused',
        5 => 'abandoned_cart',
        6 => 'pix_pending',
        7 => 'pix_paid',
        8 => 'billet_expired',
        9 => 'pix_expired',
    ];

    private array $sentStatus = [
        1 => 'error',
        2 => 'success',
        3 => 'canceled',
    ];

    public function getEvent($event)
    {
        return (is_numeric($event) ? $this->events[$event] : array_search($event, $this->events)) ?? '';
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function getSentStatus($status)
    {
        return (is_numeric($status) ? $this->sentStatus[$status] : array_search($status, $this->sentStatus)) ?? '';
    }
}
