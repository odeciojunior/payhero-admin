<?php

namespace Modules\Core\Services;


use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;

class AccountHealthService
{
    public function getAccountScore(User $user): ?float
    {
        if (!$this->userHasMinimumSalesAmount()) {
            return null;
        }

        $startDate = new \DateTime(date('Y-m-d', strtotime("-140 days")));
        $endDate = new \DateTime(date('Y-m-d', strtotime("-20 days")));

        $chargebackScore = $this->getChargebackScore($user, $startDate, $endDate);
        $attendanceScore = 0; // TODO: attendanceScore
        $trackingScore = 0; // TODO: trackingScore

        $avgScore = ($chargebackScore + $attendanceScore + $trackingScore) / 3;

        return $avgScore;
    }

    public function userHasMinimumSalesAmount(User $user)
    {
        //TODO: Sale::where ...
        $salesCount = 0;
        $minimumSalesToEvaluate = 100;
        return $salesCount > $minimumSalesToEvaluate;
    }

    public function getChargebackScore(User $user, $startDate, $endDate)
    {
        $chargebackService = new ChargebackService();
        $chargebackRate = $chargebackService->getChargebackRateInPeriod($user, $startDate, $endDate);
        $maxScore = 10;
        $chargebackScoreReference = 0.3; // each 0.3% of chargebacks rate means -1 point of score
        $score = $maxScore - $chargebackRate / $chargebackScoreReference;

        return $score;
    }
}
