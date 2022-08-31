<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\UserRegisteredEvent;
use Modules\Core\Events\UserRegistrationFinishedEvent;
use Modules\Core\Services\UserService;

class UserDocumentBureauValidationListener implements ShouldQueue
{
    use Queueable;

    private UserService $userService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param UserRegisteredEvent $event
     * @return void
     */
    public function handle(UserRegistrationFinishedEvent $event)
    {
        try {
            $user = $event->user;
            $service = new UserService();
            $service->updateUserDataFromBureau($user->document);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
