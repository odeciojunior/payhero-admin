<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\UserNotification;
use Modules\Core\Events\UserRegistrationEvent;

/**
 * Class UserRegistrationListener
 * @package Modules\Core\Listeners
 */
class UserRegistrationListener implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     * @param UserRegistrationEvent $event
     * @return void
     */
    public function handle(UserRegistrationEvent $event)
    {
        try {
            $user = $event->user ?? null;
            if (!empty($user)) {
                $user->load(["userNotification"]);
                $userNotification = $user->userNotification ?? collect();
                if ($userNotification->isEmpty()) {
                    UserNotification::create(
                        [
                            "user_id" => $user->id,
                        ]
                    );
                }
            }

            return;
        } catch (Exception $ex) {
            report($ex);

            return;
        }
    }
}
