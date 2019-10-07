<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Events\DomainApprovedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notifications\Notifications\BoletoCompensatedNotification;
use Modules\Notifications\Notifications\DomainApprovedNotification;

class DomainApprovedNotifyUserListener
{
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
        try {

            $project = $event->project;
            $users   = $event->users;
            $message = '';
            foreach ($users as $user) {
                $message = 'Domínio aprovado com sucesso para o projeto ' . $project->name . '.';

                $user->notify(new DomainApprovedNotification($message, $project->id));
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar notificação dominio aprovado');
            report($e);
        }
    }
}
