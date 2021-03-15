<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Interfaces\AchievementCheck;

class CapitalistOrbit extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 10;

    public function userAchieved(User $user): bool
    {
        $totalTransferedWithdrawals = Company::whereHas('withdrawals', function ($query) {
            $query->where('status', Withdrawal::STATUS_TRANSFERED);
        })->where('user_id', $user->id)->count();

        return $totalTransferedWithdrawals >= 50;
    }
}
