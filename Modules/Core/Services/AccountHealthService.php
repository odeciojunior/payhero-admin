<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketScores\DeliveryDelay;
use Modules\Core\Entities\TicketScores\NonTrackableOrder;
use Modules\Core\Entities\TicketScores\TrackingCodeNotInformed;
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

    public function userHasMinimumSalesAmount(User $user): bool
    {
        $approvedSales = Sale::whereIn('status', [
            Sale::STATUS_APPROVED,
            Sale::STATUS_CHARGEBACK,
            Sale::STATUS_REFUNDED,
            Sale::STATUS_IN_DISPUTE
        ])->where(function ($query) use ($user) {
            $query->where('owner_id', $user->id)
                ->orWhere('affiliate_id', $user->id);
        });

        $approvedSalesAmount = $approvedSales->count();
        $minimumSalesToEvaluate = 100;
        return $approvedSalesAmount >= $minimumSalesToEvaluate;
    }

    public function getAttendanceScore(User $user): float
    {
        /** Response Time Score */
        $averageResponseTime = $this->attendanceService->getAverageResponseTimeInDays($user);
        $responseTimeScore = 0;
        $maxResponseTimeScore = 10;
        $minimumDaysToMaxScore = 1;
        if ($averageResponseTime <= $minimumDaysToMaxScore) {
            $responseTimeScore = 10;
        } else if ($averageResponseTime <= 5) {
            $responseTimeScore = ($maxResponseTimeScore + $minimumDaysToMaxScore) - ($averageResponseTime * 2);
        }

        /** Complaint Tickets Score */
        $ticketsScore = $this->getTicketsScore($user);

        /** Tickets per Approved Sale Score */
        $ticketsPerApprovedSaleScore = $this->getTicketsPerApprovedSaleScore($user);

        /** Attendance Score */
        $attendanceScore = ($responseTimeScore + $ticketsScore + $ticketsPerApprovedSaleScore) / 3;
        return round($attendanceScore, 1);
    }

    public function getTicketsPerApprovedSaleScore(User $user): float
    {
        $startDate = now()->startOfDay()->subDays(41);
        $endDate = now()->endOfDay()->subDay();
        $rate = $this->attendanceService->getTicketsPerApprovedSaleRate($user, $startDate, $endDate);
        $maxScore = 10;
        $score = round($maxScore - $rate, 2);
        return $score > 0 ? $score : 0;
    }

    private function getTicketsScore(User $user): float
    {
        $startDate = now()->startOfDay()->subDays(41);
        $endDate = now()->endOfDay()->subDay();
        $complaintTickets = $this->attendanceService->getComplaintTicketsInPeriod($user, $startDate, $endDate);
        $totalComplaintTickets = count($complaintTickets);
        $totalScore = 0;

        if (!$totalComplaintTickets) {
            return 10;
        } else if ($totalComplaintTickets <= 20) {
            return 6;
        }

        foreach ($complaintTickets as $ticket) {
            $totalScore += $this->getTicketScore($ticket);
        }
        return round($totalScore / $totalComplaintTickets, 1);
    }

    private function getTicketScore(Ticket $ticket): int
    {
        $scores = [
            //Delivered Items
            Ticket::SUBJECT_DIFFERS_FROM_ADVERTISED    => 0,
            Ticket::SUBJECT_DAMAGED_BY_TRANSPORT       => 4,
            Ticket::SUBJECT_MANUFACTURING_DEFECT       => 4,

            //Not delivered Items
            Ticket::SUBJECT_TRACKING_CODE_NOT_RECEIVED => (new TrackingCodeNotInformed)->calculateScore($ticket),
            Ticket::SUBJECT_NON_TRACKABLE_ORDER        => (new NonTrackableOrder)->calculateScore($ticket),
            Ticket::SUBJECT_DELIVERY_DELAY             => (new DeliveryDelay)->calculateScore($ticket),
            Ticket::SUBJECT_DELIVERY_TO_WRONG_ADDRESS  => 0,

            //Others
            Ticket::SUBJECT_OTHERS                     => 10,
        ];

        return isset($scores[$ticket->subject_enum]) ? $scores[$ticket->subject_enum] : 0;
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

    public function updateAccountScore(User $user): bool
    {
        try {
            if (!$this->userHasMinimumSalesAmount($user)) {
                Log::info('Não existem transações suficientes até a data de ' . now()->format('d/m/Y') . ' para calcular o score do usuário ' . $user->name . '.');
                return false;
            }

            $startDate = now()->startOfDay()->subDays(150);
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

            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
