<?php

namespace Modules\Core\Listeners;

use Modules\Core\Events\NewChargebackEvent;
use Modules\Core\Services\EmailService;

class SendChargebackNotificationsListener
{
    public function __construct()
    {
        //
    }

    public function handle(NewChargebackEvent $event)
    {
        (new EmailService())->sendEmailChargeback($event->sale);
    }
}
