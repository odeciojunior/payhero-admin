<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class InfinityAndBeyond extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 12;

    public function userAchieved(User $user): bool
    {
        $orderBumpAndUpsellSalesCount = Sale::where('owner_id', $user->id)
            ->whereIn('status', [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE
            ])
            ->where(function ($query) {
                $query->where('has_order_bump', true)->orWhereNotNull('upsell_id');
            })->count();

        return $orderBumpAndUpsellSalesCount >= 50;
    }
}
