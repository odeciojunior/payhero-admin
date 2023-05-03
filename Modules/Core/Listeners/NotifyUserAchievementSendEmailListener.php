<?php

namespace Modules\Core\Listeners;

use App\Console\Commands\UpdateUserAchievements;
use Modules\Core\Entities\DashboardNotification;
use Modules\Core\Events\NotifyUserAchievementEvent;
use Modules\Core\Services\SendgridService;

/**
 * Class NotifyUserAchievementSendEmailListener
 * @package Modules\Core\Listeners
 */
class NotifyUserAchievementSendEmailListener
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
     * @param  NotifyUserAchievementEvent  $event
     * @return void
     */
    public function handle(NotifyUserAchievementEvent $event)
    {
        DashboardNotification::firstOrCreate([
            "user_id" => $event->user->id,
            "subject_id" => $event->achievement->id,
            "subject_type" => UpdateUserAchievements::class,
        ]);

        $sendgrindService = new SendgridService();
        $data = $event->achievement->toArray();

        $sendgrindService->sendEmail(
            "help@nexuspay.com.br",
            "nexuspay",
            $event->user->email,
            $event->user->name,
            "not", // done
            $data
        );
    }
}
