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
        $userEmail       = $event->user->email;
        $userName        = $event->user->name;
        $resetLink       = getenv('APP_URL') . "/password/reset/" . $event->token . '?email=' . $userEmail;
        $data            = [
            'name'       => $userName,
            'reset_link' => $resetLink,
        ];
        $sendGridService->sendEmail('help@cloudfox.net', 'cloudfox', $userEmail, $userName, 'd-190c876dba7a4e94bcb767a95f398ae0', $data);
    }
}
