<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Benefit;
use Modules\Core\Entities\User;

/**
 * Class BenefitService
 * @package Modules\Core\Services
 */
class BenefitService
{

    public function getUserBenefits(User $user): array
    {

        $activeBenefits = $user->benefits;
        $nextBenefits = Benefit::select('id', 'name', 'level', 'description')->where('level', $user->level+1)->orderBy('id')->get();

        foreach ($activeBenefits as &$activeBenefit) {
            $activeBenefit->name = __('definitions.benefit.'.$activeBenefit->name);
            $activeBenefit->status = 0;
        }

        foreach ($nextBenefits as &$nextBenefit) {
            $nextBenefit->name = __('definitions.benefit.'.$nextBenefit->name);
        }

        return  [
            'active' => $activeBenefits,
            'next' => $nextBenefits,
        ];

    }
}
