<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class MeteoricSupport extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 2;

    public function didUserAchieve(User $user): bool
    {
        return true; //throw new Exception('not implemented');
    }
}
