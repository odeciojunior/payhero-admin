<?php

namespace Modules\Core\Listeners;

use Modules\Core\Events\NotifyUserLevelUpdateEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Core\Services\Performance\UserLevel;
use Modules\Core\Services\SendgridService;

class NotifyUserLevelUpdateSendEmailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotifyUserLevelUpdateEvent  $event
     * @return void
     */
    public function handle(NotifyUserLevelUpdateEvent $event)
    {
        $sendgrindService = new SendgridService();

        $data = is_int($event->achievement) ?
            (new UserLevel())->getLevelData($event->user->level) : $event->achievement->toArray();
        $data['type'] = is_int($event->achievement) ?? false;

//        dd($data);
        $sendgrindService->sendEmail(
            'noreply@cloudfox.net',
            'cloudfox',
            $event->user->email,
            $event->user->name,
            'd-ee2628cce4c64ef5bbcafe3594fee27b',
            $data
        );
    }
}
