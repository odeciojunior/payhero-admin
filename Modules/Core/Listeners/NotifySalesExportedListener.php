<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Events\SalesExportedEvent;
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

        } catch (Exception $e) {
            Log::warning('Erro listener NotifySalesExportedListener');
            report($e);
        }
    }
}
