<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class Colonizer extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 3;

    public function userAchieved(User $user): bool
    {
        $activeInvites = Invitation::join("users", "user_invited", "=", "users.id")
            ->where("invite", $user->id)
            ->where("invitations.status", Invitation::INVITATION_ACCEPTED)
            ->count();

        return $activeInvites >= 10;
    }
}
