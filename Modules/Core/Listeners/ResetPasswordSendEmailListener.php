<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\ResetPasswordEvent;
use Modules\Core\Services\SendgridService;

class ResetPasswordSendEmailListener implements ShouldQueue
{
    use Queueable;

    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param ResetPasswordEvent $event
     */
    public function handle(ResetPasswordEvent $event)
    {
        $sendGridService = new SendgridService();
        $userEmail = $event->user->email;
        $userName = $event->user->name;
        $resetLink = getenv("APP_URL") . "/password/reset/" . $event->token . "?email=" . $userEmail;
        $data = [
            "name" => $userName,
            "reset_link" => $resetLink,
        ];
        $sendGridService->sendEmail(
            "noreply@nexuspay.com.br",
            "NexusPay",
            $userEmail,
            $userName,
            "d-fa32f9dd9cff4c29b27daf22d6aeafc7", /// done
            $data
        );
    }
}
