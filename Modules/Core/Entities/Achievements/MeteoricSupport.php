<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class MeteoricSupport extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 2;

    public function userAchieved(User $user): bool
    {
        return $user->attendance_average_response_time > 0 && $user->attendance_average_response_time <= 3;
    }
}
