<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class StarWars extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 7;

    public function didUserAchieve(User $user): bool
    {
        return true; //throw new Exception('not implemented');
    }
}
