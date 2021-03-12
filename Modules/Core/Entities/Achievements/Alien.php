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
        $totalCheckouts = User::join('sales', function ($query) {
            $query->on('owner_id', '=', 'users.id')
                ->where('payment_method', Sale::PAYMENT_TYPE_BANK_SLIP)
                ->whereIn('sales.status', [
                    Sale::STATUS_APPROVED,
                    Sale::STATUS_CHARGEBACK,
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_IN_DISPUTE
                ]);
        })->join('checkouts', function ($query) {
            $query->on('checkouts.id', '=', 'sales.checkout_id')
                ->where('payment_method', Sale::PAYMENT_TYPE_BANK_SLIP)
                ->whereIn('checkouts.status_enum', [
                    Checkout::STATUS_ABANDONED_CART,
                    Checkout::STATUS_RECOVERED
                ]);
        })->where('owner_id', $user->id)->count();

        $recoveredCheckouts = User::join('sales', function ($query) {
            $query->on('owner_id', '=', 'users.id')
                ->where('payment_method', Sale::PAYMENT_TYPE_BANK_SLIP)
                ->whereIn('sales.status', [
                    Sale::STATUS_APPROVED,
                    Sale::STATUS_CHARGEBACK,
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_IN_DISPUTE
                ]);
        })->join('checkouts', function ($query) {
            $query->on('checkouts.id', '=', 'sales.checkout_id')
                ->where('payment_method', Sale::PAYMENT_TYPE_BANK_SLIP)
                ->whereIn('checkouts.status_enum', [
                    Checkout::STATUS_RECOVERED
                ]);
        })->where('owner_id', $user->id)->count();

        return ($recoveredCheckouts / $totalCheckouts * 100) >= 6;
    }
}
