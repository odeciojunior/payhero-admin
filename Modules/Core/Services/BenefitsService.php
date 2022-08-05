<?php


namespace Modules\Core\Services;


use Modules\Core\Entities\User;
use Modules\Dashboard\Transformers\BenefitCollection;
use Spatie\Activitylog\Models\Activity;

class BenefitsService
{
    public static function updateUserBenefits(User $user)
    {
        if ($user->ignore_automatic_benefits_updates) {
            activity()->on($user)->tap(
                function (Activity $activity) use ($user) {
                    $activity->log_name = 'benefits_change_ignored';
                    $activity->subject_id = $user->id;
                }
            )->log('Atualização dos benefícios ignorada');
            return;
        }
        if (!$user->relationLoaded('benefits')) {
            $user->load('benefits');
        }

        // self::updateUserCashback($user);
    }

    private static function updateUserCashback(User $user)
    {
        if (!$user->relationLoaded('benefits')) {
            $user->load('benefits');
        }

        $benefits  = $user->benefits;
        $cashback1 = $benefits->where('name', 'cashback_1')->first();
        $cashback2 = $benefits->where('name', 'cashback_2')->first();

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
            if ($user->level >= 3) {
                $user->installment_cashback = 1;
                $cashback1->enabled = 0;
                $cashback2->enabled = 1;
            } elseif ($user->level == 2) {
                $user->installment_cashback = 0.5;
                $cashback1->enabled = 1;
                $cashback2->enabled = 0;
            } else {
                $user->installment_cashback = 0;
                $cashback1->enabled = 0;
                $cashback2->enabled = 0;
            }
        } else {
            $user->installment_cashback = 0;
            $cashback1->enabled = 0;
            $cashback2->enabled = 0;

            activity()->on($user)->tap(
                function (Activity $activity) use ($user) {
                    $activity->log_name = 'benefits_change';
                    $activity->subject_id = $user->id;
                }
            )->log('Cashback desativado por nota da conta menor que 6 ou desconto na taxa de parcelamento');
        }

        $cashback1->save();
        $cashback2->save();
        $user->save();
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
