<?php


namespace Modules\Core\Services;


use Illuminate\Database\Eloquent\Model;
use Modules\Dashboard\Transformers\BenefitCollection;

class BenefitsService
{
    public static function updateUserCashback(Model $user)
    {
        if (!$user->relationLoaded('benefits')) {
            $user->load('benefits');
        }
        $benefits = $user->benefits;

        $cashback2 = $benefits->where('name', 'cashback_2')
            ->where('enabled', 1)
            ->first();
        $cashback1 = $benefits->where('name', 'cashback_1')
            ->where('enabled', 1)
            ->first();
        if (!is_null($cashback2) && $user->installment_cashback != 1) {
            $user->installment_cashback = 1;
            $user->save();
            if (!is_null($cashback1)) {
                $cashback1->enabled = 0;
                $cashback1->save();
            }
        } else if (!is_null($cashback1) && $user->installment_cashback != 0.5) {
            $user->installment_cashback = 0.5;
            $user->save();
            if (!is_null($cashback2)) {
                $cashback2->enabled = 0;
                $cashback2->save();
            }
        } else if (is_null($cashback1) && is_null($cashback2) && $user->installment_cashback != 0) {
            $user->installment_cashback = 0;
            $user->save();
        }
    }

    public function getUserBenefits(Model $user): array
    {
        $benefits = $user->benefits;
        $activeBenefits = $benefits->where('enabled', 1);
        $nextBenefits = $benefits->where('level', $user->level + 1)
            ->where('enabled', 0);
//        $disabledBenefits = $benefits->where('level', '<=', $user->level)
//            ->where('enabled', 0)
//            ->reject(function ($item) use ($activeBenefits) {
//                return $activeBenefits->where('name', 'cashback_2')->count()
//                    && $item->name == 'cashback_1'
//                    && $item->enabled == 0;
//            });

        return [
            'active' => new BenefitCollection($activeBenefits),
            //'disabled' => new BenefitCollection($disabledBenefits),
            'next' => new BenefitCollection($nextBenefits),
        ];
    }
}
