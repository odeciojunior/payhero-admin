<?php

namespace Modules\Core\Listeners;

use App\Console\Commands\UpdateUserLevel;
use Modules\Core\Entities\DashboardNotification;
use Modules\Core\Events\NotifyUserLevelEvent;
use Modules\Core\Services\Performance\UserLevel;
use Modules\Core\Services\SendgridService;

/**
 * Class NotifyUserLevelSendEmailListener
 * @package Modules\Core\Listeners
 */
class NotifyUserLevelSendEmailListener
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
     * @param  NotifyUserLevelEvent  $event
     * @return void
     */
    public function handle(NotifyUserLevelEvent $event)
    {
        DashboardNotification::firstOrCreate(
            [
                'user_id' => $event->user->id,
                'subject_id' => $event->level,
                'subject_type' => UpdateUserLevel::class,
            ]
        );
        
        $sendgrindService = new SendgridService();

        $data = (new UserLevel())->getLevelData($event->user->level);
        $data['type'] = true;

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
