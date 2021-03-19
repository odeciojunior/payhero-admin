<?php

namespace Modules\Core\Listeners;

use App\Console\Commands\UpdateUserAchievements;
use App\Console\Commands\UpdateUserLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\DashboardNotification;
use Modules\Core\Entities\User;
use Modules\Core\Events\NotifyUserLevelUpdateEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUserLevelUpdateCreateListener
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
        $subject_id   = is_int($event->achievement) ? $event->achievement : $event->achievement->id;
        $subject_type = is_int($event->achievement) ? UpdateUserLevel::class : UpdateUserAchievements::class;

        DashboardNotification::firstOrCreate(
            [
                'user_id' => $event->user->id,
                'subject_id' => $subject_id,
                'subject_type' => $subject_type,
            ]
        );
    }
}
