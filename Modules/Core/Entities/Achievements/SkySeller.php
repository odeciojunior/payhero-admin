<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class SkySeller extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 4;

    public function userAchieved(User $user): bool
    {
        $totalCreditCardApprovedSales = Sale::where('payment_method', Sale::PAYMENT_TYPE_CREDIT_CARD)
            ->whereIn('status', [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE
            ])->where(function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhere('affiliate_id', $user->id);
            })->count();

        return $totalCreditCardApprovedSales >= 1000;
    }
}
