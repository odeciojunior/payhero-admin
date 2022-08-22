<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class Alien extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 8;

    public function userAchieved(User $user): bool
    {
        $totalCheckouts = User::join("users_projects", function ($query) use ($user) {
            $query->on("users.id", "=", "user_id")->where("user_id", $user->id);
        })
            ->join("checkouts", function ($query) use ($user) {
                $query
                    ->on("checkouts.project_id", "=", "users_projects.project_id")
                    ->whereIn("checkouts.status_enum", [Checkout::STATUS_ABANDONED_CART, Checkout::STATUS_RECOVERED]);
            })
            ->count();

        $recoveredCheckouts = Sale::where("payment_method", "=", Sale::PAYMENT_TYPE_BANK_SLIP)
            ->whereIn("sales.status", [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE,
            ])
            ->join("checkouts", function ($query) {
                $query
                    ->on("checkouts.id", "=", "sales.checkout_id")
                    ->whereRaw("payment_method = " . Sale::PAYMENT_TYPE_BANK_SLIP)
                    ->whereIn("checkouts.status_enum", [Checkout::STATUS_RECOVERED]);
            })
            ->where(function ($query) use ($user) {
                $query->where("sales.owner_id", $user->id)->orWhere("sales.affiliate_id", $user->id);
            })
            ->count();

        if (!$recoveredCheckouts || !$totalCheckouts) {
            return false;
        }

        return $recoveredCheckouts / $totalCheckouts >= 0.06;
    }
}
