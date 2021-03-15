<?php


namespace Modules\Core\Services;


use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\Benefit;
use Modules\Dashboard\Transformers\BenefitCollection;

class BenefitsService
{
    public static function updateUserCashback(Model $user)
    {
        if (!$user->relationLoaded('benefits')) {
            $user->load('benefits');
        }
        $cashback = $user->benefits
            ->whereStrict('original_name', 'cashback')
            ->where('disabled', 0)
            ->first();
        if (!is_null($cashback) && $user->level > 2 && $user->installment_cashback != 1) {
            $user->installment_cashback = 1;
            $user->save();
        } else if (!is_null($cashback) && $user->level == 2 && $user->installment_cashback < 0.5) {
            $user->installment_cashback = 0.5;
            $user->save();
        } else if (is_null($cashback) && $user->installment_cashback != 0) {
            $user->installment_cashback = 0;
            $user->save();
        }
    }

    public function getUserBenefits(Model $user): array
    {
        $activeBenefits = $user->benefits;

        $nextLevel = $user->level + 1;
        $nextBenefits = Benefit::select('id', 'name', 'level', 'description')
            ->where('level', $nextLevel)
            ->whereDoesntHave('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('id')
            ->get();
        if ($nextLevel == 3 && $user->installment_cashback == 0.5) {
            $nextCashback = Benefit::where('name', 'cashback')->first();
            $nextCashback->level = 3;
            $nextCashback->installment_cashback = 1;
            $nextBenefits->push($nextCashback);
        }

        return  [
            'active' => new BenefitCollection($activeBenefits),
            'next'    => new BenefitCollection($nextBenefits),
        ];
    }
}
