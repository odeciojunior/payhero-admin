<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Benefit;
use Modules\Core\Entities\User;
use Modules\Dashboard\Transformers\BenefitCollection;;

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


        return  [
            'active' => new BenefitCollection($activeBenefits),
            'next'    => new BenefitCollection($nextBenefits),
        ];

    }
}
