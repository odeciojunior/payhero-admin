<?php

namespace Modules\Core\Listeners;

use Modules\Core\Events\BoletoPaidEvent;
use Modules\Notifications\Notifications\BoletoCompensatedNotification;

class BoletoPaidNotifyUser
{
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * @param BoletoPaidEvent $event
     * @return void
     */
    public function handle(BoletoPaidEvent $event)
    {
        $user        = $event->data['user'];
        $boletoCount = $event->data['boleto_count'];
        $user->notify(new BoletoCompensatedNotification($boletoCount));
    }
}
