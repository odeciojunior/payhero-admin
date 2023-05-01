<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Events\ExtractExportedEvent;
use Modules\Core\Services\SendgridService;
use Modules\Notifications\Notifications\SalesExportedNotification;

class NotifyExtractExportedListener
{
    /**
     * Handle the event.
     * @param ExtractExportedEvent $event
     * @return void
     */
    public function handle(ExtractExportedEvent $event)
    {
        try {
            $user = $event->user ?? null;
            $filename = $event->filename;
            $userEmail = !empty($event->email) ? $event->email : $user->email;

            if (!str_contains($userEmail, "@nexuspay.com.br")) {
                Notification::send($user, new SalesExportedNotification($user, $filename));
            }

            //Envio de e-mail
            $sendGridService = new SendgridService();
            $userName = $user->name;
            $downloadLink = getenv("APP_URL") . "/sales/download/" . $filename;

            $data = [
                "name" => $userName,
                "report_name" => "Relatório extrato financeiro",
                "download_link" => $downloadLink,
            ];

            $sendGridService->sendEmail(
                "help@nexuspay.com.br",
                "NexusPay - Relatório extrato financeiro",
                $userEmail,
                $userName,
                "d-367113d653654dfd84abd5134f232d99", // done
                $data
            );
        } catch (Exception $e) {
            Log::warning("Erro listener NotifyExtractExportedListener");
            report($e);
        }
    }
}
