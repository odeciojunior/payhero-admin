<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Events\TrackingsExportedEvent;
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
            $user     = $event->user ?? null;
            $filename = $event->filename;
            Notification::send($user, new TrackingsExportedNotification($user, $filename));
        } catch (Exception $e) {
            Log::warning('Erro listener NotifyTrackingsExportedListener');
            report($e);
        }
    }
}
