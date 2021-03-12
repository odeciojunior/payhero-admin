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

        $cashback = $user->benefits->where('name', 'Cashback')
            ->where('disabled', 0)
            ->first();
        if (!is_null($cashback) && $user->level > 2 && $user->installment_cashback != 1) {
            $user->installment_cashback = 1;
            $user->save();
        } else if (!is_null($cashback) && $user->level == 2 && $user->installment_cashback != 0.5) {
            $user->installment_cashback = 0.5;
            $user->save();
        } else if ($user->installment_cashback != 0) {
            $user->installment_cashback = 0;
            $user->save();
        }
    }
}
