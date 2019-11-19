<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Events\TrackingsImportedEvent;
use Modules\Notifications\Notifications\TrackingsImportedNotification;

class NotifyTrackingsImportedListener
{
    /**
     * Handle the event.
     * @param TrackingsImportedEvent $event
     * @return void
     */
    public function handle(TrackingsImportedEvent $event)
    {
        try {
            $user = $event->user ?? null;
            Notification::send($user, new TrackingsImportedNotification($user));

        } catch (Exception $e) {
            Log::warning('Erro listener NotifyTrackingsImportedListener');
            report($e);
        }
    }
}
