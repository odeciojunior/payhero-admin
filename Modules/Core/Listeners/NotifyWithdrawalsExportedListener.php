<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Events\WithdrawalsExportedEvent;
use Modules\Core\Services\SendgridService;
use Modules\Notifications\Notifications\WithdrawalsExportedNotification;

class NotifyWithdrawalsExportedListener
{
    /**
     * Handle the event.
     * @param WithdrawalsExportedEvent $event
     * @return void
     */
    public function handle(WithdrawalsExportedEvent $event)
    {
        try {
            $user = $event->user ?? null;
            $filename = $event->filename;
            $userEmail = !empty($event->email) ? $event->email : $user->email;

            Notification::send($user, new WithdrawalsExportedNotification($user, $filename));

            //Envio de e-mail
            $sendGridService = new SendgridService();
            $userName = $user->name;
            $downloadLink = getenv("APP_URL") . "/withdrawals/download/" . $filename;

            $data = [
                "name" => $userName,
                "report_name" => "Relatório de Transferências",
                "download_link" => $downloadLink,
                "subject" => "Relatório",
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
            Log::warning("Erro listener NotifyWithdrawalsExportedListener");
            report($e);
        }
    }
}
