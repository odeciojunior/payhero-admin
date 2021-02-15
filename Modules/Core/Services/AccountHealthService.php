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
    }

    public function getAccountScore(User $user): ?float
    {
        if (!$this->userHasMinimumSalesAmount($user)) {
            return null;
        }

        $startDate = now()->startOfDay()->subDays(140);
        $endDate = now()->endOfDay()->subDays(20);

        $chargebackScore = $this->getChargebackScore($user, $startDate, $endDate);
        $attendanceScore = $this->getAttendanceScore($user, $startDate, $endDate);
        $trackingScore = $this->getTrackingScore($user, $startDate, $endDate);

        $accountScore = ($chargebackScore + $attendanceScore + $trackingScore) / 3;

        return $accountScore;
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

    public function getAttendanceScore(User $user, $startDate, $endDate): float
    {
        return 0;
    }

    public function getChargebackScore(User $user, $startDate, $endDate): float
    {
        $chargebackService = new ChargebackService();
        $chargebackRate = $chargebackService->getChargebackRateInPeriod($user, $startDate, $endDate);
        $maxScore = 10;
        $chargebackScoreReference = 0.3; // each 0.3% of chargebacks rate means -1 point of score
        $score = $maxScore - $chargebackRate / $chargebackScoreReference;

        return $score;
    }

    public function getTrackingScore(User $user, $startDate, $endDate): float
    {
        return 0;
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
            $endDate = now()->endOfDay()->subDays(20); //TODO: change back to subdays 20 after tests

            $chargebackScore = $this->getChargebackScore($user, $startDate, $endDate);
            $attendanceScore = $this->getAttendanceScore($user, $startDate, $endDate);
            $trackingScore = $this->getTrackingScore($user, $startDate, $endDate);

            $accountScore = ($chargebackScore + $attendanceScore + $trackingScore) / 3;

            $user->chargeback_tax = $this->chargebackService->getChargebackRateInPeriod($user, $startDate, $endDate);
            $user->account_score = $accountScore;
            $user->chargeback_score = $accountScore;
            $user->attendance_score = $accountScore;
            $user->tracking_score = $accountScore;
            $user->attendance_average_response_time = 0;
            $user->save();

            //DB::commit();
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
            dd($e->getMessage());
            DB::rollBack();
        }
    }
}
