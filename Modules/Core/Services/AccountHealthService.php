<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;

class AccountHealthService
{
    public function __construct()
    {
        $this->chargebackService = new ChargebackService();
        $this->trackingService = new TrackingService();
        //TODO: $this->attendanceService = new AttendanceService();
    }

    public function userHasMinimumSalesAmount(User $user)
    {
        $approvedSales = Sale::where('gateway_id', 15)
            ->where('payment_method', Sale::PAYMENT_TYPE_CREDIT_CARD)
            ->whereIn('status', [
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
        $startDate = now()->startOfDay()->subDays(140);
        $endDate = now()->endOfDay()->subDays(20);
        return 0;
    }

    public function getChargebackScore(User $user): float
    {
        $startDate = now()->startOfDay()->subDays(140);
        $endDate = now()->endOfDay()->subDays(20);
        $chargebackRate = $this->chargebackService->getChargebackRateInPeriod($user, $startDate, $endDate);
        $maxScore = 10;
        //each 0.3% of chargebacks rate means -1 point of score
        $chargebackScoreReference = 0.3;
        $score = $maxScore - $chargebackRate / $chargebackScoreReference;

        return $score;
    }

    public function testTrackingScore(User $user): array
    {
        return [
            'user: ' . $user->name,
            'averagePostingTimeScore: ' . $averagePostingTimeScore = $this->getAveragePostingTimeScore($user),
            'uninformedTrackingScore: ' . $uninformedTrackingScore = $this->getUninformedTrackingCodeScore($user),
            'trackingCodeProblemScore: ' . $trackingCodeProblemScore = $this->getTrackingCodeProblemScore($user),
            'calculated score: ' . (($averagePostingTimeScore * 2) + $uninformedTrackingScore + $trackingCodeProblemScore) / 4,
            'score by method: ' . $this->getTrackingScore($user)
        ];
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
        if ($avgPostingTime < 2) {
            $score = 10;
        } else if ($avgPostingTime < 10) {
            $score = 12 - (int)$avgPostingTime;
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
            DB::beginTransaction();

            if (!$this->userHasMinimumSalesAmount($user)) {
                Log::warning('Não existem transações suficientes até a data de ' . now()->format('d/m/Y') . ' para calcular o score do usuário ' . $user->name . '.');
                return;
            }

            $startDate = now()->startOfDay()->subDays(140);
            $endDate   = now()->endOfDay()->subDays(20);
            $chargebackTax   = $this->chargebackService->getChargebackRateInPeriod($user, $startDate, $endDate);
            $attendanceScore = $this->getAttendanceScore($user);
            $chargebackScore = $this->getChargebackScore($user);
            $trackingScore   = $this->getTrackingScore($user);
            $accountScore    = ($chargebackScore + $attendanceScore + $trackingScore) / 3;

            $user->account_score    = $accountScore;
            $user->attendance_score = $attendanceScore;
            $user->chargeback_score = $chargebackScore;
            $user->chargeback_tax   = $chargebackTax;
            $user->tracking_score   = $trackingScore;
            $user->save();

            DB::commit();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
        }
    }
}
