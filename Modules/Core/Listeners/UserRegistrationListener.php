<?php

namespace Modules\Core\Listeners;

use Exception;
use Modules\Core\Entities\UserNotification;
use Modules\Core\Events\UserRegistrationEvent;

/**
 * Class UserRegistrationListener
 * @package Modules\Core\Listeners
 */
class UserRegistrationListener
{
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
                            "user_id" => $user->account_owner,
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
