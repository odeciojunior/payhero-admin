<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;

/**
 * Class AccountHealthService
 * @package Modules\Core\Services
 */
class AccountHealthService
{
    private ChargebackService $chargebackService;
    private TrackingService $trackingService;
    private AttendanceService $attendanceService;

    public function __construct()
    {
        $this->chargebackService = new ChargebackService();
        $this->trackingService = new TrackingService();
        $this->attendanceService = new AttendanceService();
    }

    public function userHasMinimumSalesAmount(User $user)
    {
        $approvedSales = Sale::whereIn('status', [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE
            ])->where('owner_id', $user->id);


        $approvedSalesAmount = $approvedSales->count();
        $minimumSalesToEvaluate = 100;
        return $approvedSalesAmount >= $minimumSalesToEvaluate;
    }

    public function getAttendanceScore(User $user): float
    {
        $averageResponseTime = $this->attendanceService->getAverageResponseTimeInDays($user);
        $score = 0;
        $maxScore = 10;
        $minimumDaysToMaxScore = 1;
        if ($averageResponseTime <= $minimumDaysToMaxScore) {
            $score = 10;
        } else if ($averageResponseTime <= 5) {
            $score = ($maxScore + $minimumDaysToMaxScore) - ($averageResponseTime * 2);
        }
        return $score;
    }

    public function getChargebackScore(User $user): float
    {
        $startDate = now()->startOfDay()->subDays(140);
        $endDate = now()->endOfDay()->subDays(20);
        $chargebackRate = $this->chargebackService->getChargebackRateInPeriod($user, $startDate, $endDate);
        $maxScore = 10;
        //each 0.3% of chargebacks rate means -1 point of score
        $chargebackScoreReference = 0.3;

        $score = 0;
        if ($chargebackRate <= 3) {
            $score = round(($maxScore - $chargebackRate / $chargebackScoreReference), 2);
        }

        return $score;
    }

    public function getTrackingScore(User $user): float
    {
        $averagePostingTimeScore = $this->getAveragePostingTimeScore($user);
        $uninformedTrackingScore = $this->getUninformedTrackingCodeScore($user);
        $trackingCodeProblemScore = $this->getTrackingCodeProblemScore($user);
        $score = (($averagePostingTimeScore * 2) + $uninformedTrackingScore + $trackingCodeProblemScore) / 4;
        return round($score, 2);
    }

    private function getAveragePostingTimeScore(User $user): float
    {
        $startDate = now()->startOfDay()->subDays(30);
        $endDate = now()->endOfDay();
        $avgPostingTime = $this->trackingService->getAveragePostingTimeInPeriod($user, $startDate, $endDate);
        $score = 0;
        $maxScore = 10;
        $minimumDaysToMaxScore = 2;
        if ($avgPostingTime < $minimumDaysToMaxScore) {
            $score = 10;
        } else if ($avgPostingTime < 10) {
            $score = $maxScore + $minimumDaysToMaxScore - $avgPostingTime;
        }
        return $score;
    }

    private function getTrackingCodeProblemScore(User $user): float
    {
        $startDate = now()->startOfDay()->subDays(90);
        $endDate = now()->endOfDay();
        $trackingCodeProblemRate = $this->trackingService->getTrackingCodeProblemRateInPeriod($user, $startDate, $endDate);
        $maxScore = 10;
        $trackingCodeProblemScoreReference = 2;
        $score = 0;
        if ($trackingCodeProblemRate < 1) {
            $score = 10;
        } else if ($trackingCodeProblemRate <= 5) {
            //each 1% of problem rate means -2 points of score
            $score = $maxScore - $trackingCodeProblemRate * $trackingCodeProblemScoreReference;
        }
        return $score;
    }

    private function getUninformedTrackingCodeScore(User $user): float
    {
        $startDate = now()->startOfDay()->subDays(20);
        $endDate = now()->endOfDay();
        $uninformedRate = $this->trackingService->getUninformedTrackingCodeRateInPeriod($user, $startDate, $endDate);
        $maxScore = 10;
        $score = 0;
        if ($uninformedRate <= 3) {
            $score = $maxScore;
        } else if ($uninformedRate <= 13) {
            //after 3% every 1% of uninformed rate means -1 point of score
            $score = ($maxScore + 3) - $uninformedRate;
        }
        return $score;
    }

    public function updateAccountScore(User $user): void
    {
        try {
            if (!$this->userHasMinimumSalesAmount($user)) {
                Log::info('Não existem transações suficientes até a data de ' . now()->format('d/m/Y') . ' para calcular o score do usuário ' . $user->name . '.');
                return;
            }

            $startDate = now()->startOfDay()->subDays(140);
            $endDate = now()->endOfDay()->subDays(20);

            $chargebackRate = $this->chargebackService->getChargebackRateInPeriod($user, $startDate, $endDate);
            $attendanceScore = $this->getAttendanceScore($user);
            $chargebackScore = $this->getChargebackScore($user);
            $trackingScore = $this->getTrackingScore($user);
            $accountScore = round(($chargebackScore + $attendanceScore + $trackingScore) / 3, 2);

            $user->update([
                'account_score'    => $accountScore,
                'attendance_score' => $attendanceScore,
                'chargeback_score' => $chargebackScore,
                'chargeback_rate'  => $chargebackRate,
                'tracking_score'   => $trackingScore
            ]);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
