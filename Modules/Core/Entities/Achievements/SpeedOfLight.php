<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class SpeedOfLight extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 1;

    public function userAchieved(User $user): bool
    {
        return $user->tracking_score >= 9;
    }
}
