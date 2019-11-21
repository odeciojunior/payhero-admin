<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\BoletoPaidEvent;
use Modules\Core\Services\SendgridService;

/**
 * Class BoletoPaidEmailNotifyUser
 * @package Modules\Core\Listeners
 */
class BoletoPaidEmailNotifyUser implements ShouldQueue
{
    use Queueable;

    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     * @param BoletoPaidEvent $event
     * @return void
     */
    public function handle(BoletoPaidEvent $event)
    {
        /** @var SendgridService $sendGridService */
        $sendGridService = new SendgridService();
        $user            = $event->data['user'];
        $data            = $event->data;
        $sendGridService->sendEmail('noreply@cloudfox.net', 'cloudfox', $user->email, $user->name, 'd-4ce62be1218d4b258c8d1ab139d4d664', $data);
    }
}
