<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Events\SalesExportedEvent;
use Modules\Core\Services\SendgridService;
use Modules\Notifications\Notifications\SalesExportedNotification;

class NotifySalesExportedListener
{
    /**
     * Handle the event.
     * @param SalesExportedEvent $event
     * @return void
     */
    public function handle(SalesExportedEvent $event)
    {
        try {
            $user = $event->user ?? null;
            $filename = $event->filename;


            Notification::send($user, new SalesExportedNotification($user, $filename));

            //Envio de e-mail
            $sendGridService = new SendgridService();
            $userEmail = $user->email;
            $userName = $user->name;
            $downloadLink = getenv('APP_URL') . "/sales/download/" . $filename;

            $data = [
                'name' => $userName,
                'report_name' => 'Relatório de Vendas',
                'download_link' => $downloadLink,
            ];

            $sendGridService->sendEmail('noreply@cloudfox.net', 'CloudFox', $userEmail, $userName, 'd-2279bf09c11a4bf59b951e063d274450', $data);

        } catch (Exception $e) {
            Log::warning('Erro listener NotifySalesExportedListener');
            report($e);
        }
    }
}
