<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\NewChargebackEvent;
use Modules\Core\Services\EmailService;

class SendChargebackNotificationsListener implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(NewChargebackEvent $event)
    {
        (new EmailService())->sendEmailChargeback($event->sale);
    }
}
