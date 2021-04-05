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
        DashboardNotification::firstOrCreate(
            [
                'user_id' => $event->user->id,
                'subject_id' => $event->achievement->id,
                'subject_type' => UpdateUserAchievements::class,
            ]
        );

//        $sendgrindService = new SendgridService();
//
//        $benefits = $event->user->benefits->where('enabled', true)->where('level', $event->user->level)->toArray();
//
//        $data['benefits'] = null;
//        if (!isEmpty($benefits)) {
//            $benefitsDescription = array_column($benefits, 'description');
//            $data['benefits'] = $this->arrayToString($benefitsDescription);
//        }
//
//        $sendgrindService->sendEmail(
//            'noreply@cloudfox.net',
//            'cloudfox',
//            $event->user->email,
//            $event->user->name,
//            'd-31354085bb7e441597f76fdb6e94d182',
//            $data
//        );
    }

    public function arrayToString($array)
    {
        $lastItem = array_pop($array);
        $text = implode(', ', $array);
        $text .= ' e '.$lastItem;

        return $text;
    }
}
