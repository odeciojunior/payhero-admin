<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class Moonstruck extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 11;

    public function userAchieved(User $user): bool
    {
        return false;
    }
}
