<?php

namespace Modules\Core\Interfaces;

use Modules\Core\Entities\User;

interface AchievementCheck
{
    public function userAchieved(User $user): bool;
}
