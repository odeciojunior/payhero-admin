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
        $totalApprovedSales = Sale::where(function ($query) use ($user) {
            $query->where("owner_id", $user->id)->orWhere("affiliate_id", $user->id);
        })
            ->whereIn("status", [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE,
            ])
            ->count();

        if ($totalApprovedSales < 100) {
            return false;
        }

        $totalBankSlipSales = Sale::where("payment_method", Sale::PAYMENT_TYPE_BANK_SLIP)
            ->where(function ($query) use ($user) {
                $query->where("owner_id", $user->id)->orWhere("affiliate_id", $user->id);
            })
            ->count();

        if ($totalBankSlipSales == 0) {
            return false;
        }

        $totalBankSlipApprovedSales = Sale::where("payment_method", Sale::PAYMENT_TYPE_BANK_SLIP)
            ->whereIn("status", [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE,
            ])
            ->where(function ($query) use ($user) {
                $query->where("owner_id", $user->id)->orWhere("affiliate_id", $user->id);
            })
            ->count();

        return $totalBankSlipApprovedSales / $totalBankSlipSales >= 0.5;
    }
}
