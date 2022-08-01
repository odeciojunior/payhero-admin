<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\User;
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
            $bureauUserData = $this->userService->getBureauUserData($user->document);
            $user->bureau_result = json_encode($bureauUserData->getRawData());
            $user->name = $bureauUserData->getName() ?: $user->name;
            if (!$bureauUserData->isAbleToCreateAccount()) {
                $user->status = User::STATUS_ACCOUNT_BLOCKED;
                $user->observation = $bureauUserData->getIssues();
            }
            $user->save();
        } catch (\Exception $e) {
            report($e);
        }
    }
}
