<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class Alien extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 8;

    public function userAchieved(User $user): bool
    {
        return false; //throw new Exception('not implemented');
    }
}
