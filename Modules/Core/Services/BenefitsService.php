<?php


namespace Modules\Core\Services;


use Modules\Core\Entities\User;
use Modules\Dashboard\Transformers\BenefitCollection;
use Spatie\Activitylog\Models\Activity;

class BenefitsService
{
    public static function updateUserCashback(User $user)
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

        if (!$user->relationLoaded('companies')) {
            $user->load('companies');
        }

        $fullInstallmentTax = true;
        foreach ($user->companies as $company) {
            if ($company->installment_tax < 2.99) {
                $fullInstallmentTax = false;
                break;
            }
        }

        // User only has cashback if it has full installment tax (at this time 2.99%)
        // and account score greater than or equal 6
        if ($fullInstallmentTax && $user->account_score >= 6) {
            if (!is_null($cashback2)) {
                if ($user->installment_cashback != 1) {
                    $user->installment_cashback = 1;
                    $user->save();
                }
                if (!is_null($cashback1)) {
                    $cashback1->enabled = 0;
                    $cashback1->save();
                }
            } else if (!is_null($cashback1)) {
                if ($user->installment_cashback != 0.5) {
                    $user->installment_cashback = 0.5;
                    $user->save();
                }
                if (!is_null($cashback2)) {
                    $cashback2->enabled = 0;
                    $cashback2->save();
                }
            } else if (is_null($cashback1) && is_null($cashback2)) {
                if ($user->installment_cashback != 0) {
                    $user->installment_cashback = 0;
                    $user->save();
                }
            }
        } else {
            if ($cashback1) {
                $cashback1->enabled = 0;
                $cashback1->save();
            }
            if ($cashback2) {
                $cashback2->enabled = 0;
                $cashback2->save();
            }
            $user->installment_cashback = 0;
            $user->save();

            activity()->on($user)->tap(
                function (Activity $activity) use ($user) {
                    $activity->log_name = 'benefits_change';
                    $activity->subject_id = $user->id;
                }
            )->log('Cashback desativado');
        }
    }

    public function getUserBenefits(User $user): array
    {
        $benefits = $user->benefits;
        $activeBenefits = $benefits->where('enabled', 1);
        $disabledBenefits = $benefits->where('level', '<=', $user->level)
            ->where('enabled', 0);
        $nextBenefits = $benefits->where('level', $user->level + 1)
            ->where('enabled', 0);

        $result = $activeBenefits->merge($disabledBenefits);
        $hasCashback1 = !!$result->where('name', 'cashback_1')
            ->where('enabled', 1)
            ->count();
        $hasCashback2 = !!$result->where('name', 'cashback_2')
            ->where('enabled', 1)
            ->count();
        $result = $result->reject(function ($item) use ($hasCashback1, $hasCashback2) {
            if ($hasCashback1 || $hasCashback2) {
                if ($item->name === 'cashback_1' && $hasCashback2) {
                    return true;
                }
                if ($item->name === 'cashback_2' && $hasCashback1) {
                    return true;
                }
            } else if ($item->name === 'cashback_2') {
                return true;
            }
            return false;
        });

        return [
            'active' => new BenefitCollection($result),
            'next'   => new BenefitCollection($nextBenefits),
        ];
    }
}
