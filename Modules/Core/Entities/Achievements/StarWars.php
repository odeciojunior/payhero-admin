<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class StarWars extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 7;

    public function userAchieved(User $user): bool
    {
        $totalApprovedSales = Sale::where('owner_id', $user->id)
            ->whereIn('status', [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE
            ])->count();

        if ($totalApprovedSales < 100) {
            return false;
        }

        $totalBankSlipSales = Sale::where('payment_method', Sale::PAYMENT_TYPE_BANK_SLIP)
            ->where('owner_id', $user->id)->count();

        $totalBankSlipApprovedSales = Sale::where('payment_method', Sale::PAYMENT_TYPE_BANK_SLIP)
            ->whereIn('status', [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE
            ])->where('owner_id', $user->id)->count();

        return ($totalBankSlipApprovedSales / $totalBankSlipSales) >= 0.5;
    }
}
