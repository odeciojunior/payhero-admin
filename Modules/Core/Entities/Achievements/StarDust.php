<?php

namespace Modules\Core\Entities\Achievements;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class StarDust extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 5;

    public function userAchieved(User $user): bool
    {
        $totalApprovedDigitalSales = Sale::where(function ($query) use ($user) {
            $query->where("owner_id", $user->id)->orWhere("affiliate_id", $user->id);
        })
            ->join("products_plans_sales", "sales.id", "=", "products_plans_sales.sale_id")
            ->join("products", "products.id", "=", "products_plans_sales.product_id")
            ->where("products.type_enum", Product::TYPE_DIGITAL)
            ->whereIn("sales.status", [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE,
            ])
            ->count();

        return $totalApprovedDigitalSales >= 100;
    }
}
