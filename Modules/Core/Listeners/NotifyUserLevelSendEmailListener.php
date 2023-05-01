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
        DashboardNotification::firstOrCreate([
            "user_id" => $event->user->id,
            "subject_id" => $event->level,
            "subject_type" => UpdateUserLevel::class,
        ]);

        $sendgrindService = new SendgridService();

        $data = (new UserLevel())->getLevelData($event->user->level);
        $benefits = $event->user->benefits->where("enabled", true)->toArray();

        $data["benefits"] = null;
        if (!empty($benefits)) {
            $benefitsDescription = array_column($benefits, "description");
            $data["benefits"] = $this->arrayToString($benefitsDescription);
        }

        $sendgrindService->sendEmail(
            "help@nexuspay.com.br",
            "NexusPay",
            $event->user->email,
            $event->user->name,
            "not", // done
            $data
        );

        if ($event->level == 3) {
            $userName = [
                "nome" => ucfirst(strtolower(current(explode(" ", $event->user->name)))),
            ];
            $sendgrindService->sendEmail(
                "help@nexuspay.com.br",
                "NexusPay",
                $event->user->email,
                $event->user->name,
                "not", // done
                $userName
            );
        }
    }

    public function arrayToString($array)
    {
        if (count($array) > 1) {
            $lastItem = array_pop($array);
            $text = implode(", ", $array);
            $text .= " e " . $lastItem;

            return $text;
        }

        return current($array);
    }
}
