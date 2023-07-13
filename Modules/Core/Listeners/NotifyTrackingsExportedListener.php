<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Events\TrackingsExportedEvent;
use Modules\Core\Services\SendgridService;
use Modules\Notifications\Notifications\TrackingsExportedNotification;

/**
 * Class NotifyTrackingsExportedListener
 * @package Modules\Core\Listeners
 */
class NotifyTrackingsExportedListener implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     * @param TrackingsExportedEvent $event
     * @return void
     */
    public function handle(TrackingsExportedEvent $event)
    {
        try {
            $user = $event->user ?? null;
            $filename = $event->filename;

            //Notificação no sistema
            Notification::send($user, new TrackingsExportedNotification($user, $filename));

            //Envio de e-mail
            $sendGridService = new SendgridService();
            $userEmail = $user->email;
            $userName = $user->name;
            $downloadLink = getenv("APP_URL") . "/trackings/download/" . $filename;

            $data = [
                "name" => $userName,
                "report_name" => "Relatório de Códigos de Rastreio",
                "download_link" => $downloadLink,
            ];

            $sendGridService->sendEmail(
                "noreply@azcend.com.br",
                "Azcend",
                $userEmail,
                $userName,
                "d-b999b01727f14c84adf4fceab77c5d3d", /// done
                $data
            );
        } catch (Exception $e) {
            Log::warning("Erro listener NotifyTrackingsExportedListener");
            report($e);
        }
    }
}
