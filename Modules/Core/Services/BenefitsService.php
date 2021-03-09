<?php


namespace Modules\Core\Services;


use Illuminate\Database\Eloquent\Model;

class BenefitsService
{
    public static function updateUserCashback(Model $user)
    {
        if (!$user->relationLoaded('benefits')) {
            $user->load('benefits');
        }

        $cashback1 = $user->benefits->where('name', 'cashback_1')
            ->where('disabled', 0)
            ->first();
        $cashback2 = $user->benefits->where('name', 'cashback_2')
            ->where('disabled', 0)
            ->first();
        if (!is_null($cashback2) && $user->installment_cashback != 1) {
            $user->installment_cashback = 1;
            $user->save();
        } else if (!is_null($cashback1) && $user->installment_cashback != 0.5) {
            $user->installment_cashback = 0.5;
            $user->save();
        } else if ($user->installment_cashback != 0) {
            $user->installment_cashback = 0;
            $user->save();
        }
    }
}
