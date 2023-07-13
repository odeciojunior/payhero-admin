<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Events\FinancesExportedEvent;
use Modules\Core\Events\SalesExportedEvent;
use Modules\Core\Services\SendgridService;
use Modules\Notifications\Notifications\FinancesExportedNotification;
use Modules\Notifications\Notifications\SalesExportedNotification;

class NotifyFinancesExportedListener
{
    /**
     * Handle the event.
     * @param FinancesExportedEvent $event
     * @return void
     */
    public function handle(FinancesExportedEvent $event)
    {
        try {
            $user = $event->user ?? null;
            $filename = $event->filename;
            $userEmail = !empty($event->email) ? $event->email : $user->email;

            if (!str_contains($userEmail, "@azcend.com.br")) {
                Notification::send($user, new FinancesExportedNotification($user, $filename));
            }

            //Envio de e-mail
            $sendGridService = new SendgridService();
            $userName = $user->name;
            $downloadLink = getenv("APP_URL") . "/finances/download/" . $filename;

            $data = [
                "name" => $userName,
                "report_name" => "Relatório de Finanças",
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
            Log::warning("Erro listener NotifySalesExportedListener");
            report($e);
        }
    }
}
