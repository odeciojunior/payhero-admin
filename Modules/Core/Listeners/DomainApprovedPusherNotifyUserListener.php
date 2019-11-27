<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\DomainApprovedEvent;
use Modules\Core\Services\PusherService;

class DomainApprovedPusherNotifyUserListener implements ShouldQueue
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
        $pusherService = new PusherService();

        $project = $event->project;
        $users   = $event->users;

        $data = [];
        foreach ($users as $user) {
            $data = [
                'message' => 'Domínio aprovado com sucesso para o projeto ' . $project->name . '',
                'user'    => $user->account_owner_id,
            ];

            $pusherService->sendPusher($data);
            unset($data);
        }
    }
}
