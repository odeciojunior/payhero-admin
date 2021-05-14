<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\BoletoPaidEvent;
use Modules\Core\Services\UserNotificationService;
use Modules\Notifications\Notifications\BoletoCompensatedNotification;

/**
 * Class BoletoPaidNotifyUser
 * @package Modules\Core\Listeners
 */
class BoletoPaidNotifyUser implements ShouldQueue
{
    use Queueable;
    /**
     * @var string
     * @description name of the column in user_notifications table to check if it will send
     */
    private $userNotification = "boleto_compensated";

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

        /** @var UserNotificationService $userNotificationService */
        $userNotificationService = app(UserNotificationService::class);
        if ($userNotificationService->verifyUserNotification($user, $this->userNotification)) {
            $user->notify(new BoletoCompensatedNotification($boletoCount));
        }
    }
}
