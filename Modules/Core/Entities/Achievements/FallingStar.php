<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class FallingStar extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 6;

    public function userAchieved(User $user): bool
    {
        return false;
    }
}
