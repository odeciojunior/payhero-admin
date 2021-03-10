<?php

namespace Modules\Core\Interfaces;

use Modules\Core\Entities\User;

interface AchievementCheck
{
    public function didUserAchieve(User $user): bool;
}
