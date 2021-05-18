<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\DomainApprovedEvent;
use Modules\Core\Services\SendgridService;

class DomainApprovedEmailNotifyUserListener implements ShouldQueue
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
     * Handle the event.
     * @param DomainApprovedEvent $event
     * @return void
     */
    public function handle(DomainApprovedEvent $event)
    {
        $sendGridService = new SendgridService();

        $users  = $event->users;
        $domain = $event->domain;

        $data = [];
        foreach ($users as $user) {
            $data = [
                'domain_name' => $domain->name,
                'first_name'  => $user->name,
            ];

            $sendGridService->sendEmail('noreply@cloudfox.net', 'cloudfox', $user->email, $user->name, 'd-b387f185f29a45b6a2009ba94f63ed1a', $data);
        }
    }
}
