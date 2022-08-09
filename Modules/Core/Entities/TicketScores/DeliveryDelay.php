<?php

namespace Modules\Core\Entities\TicketScores;

use Carbon\Carbon;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Tracking;
use Modules\Core\Interfaces\TicketScore;

class DeliveryDelay implements TicketScore
{
    public function calculateScore(Ticket $ticket): int
    {
        $ticketDate = Carbon::make($ticket->created_at)->startOfDay();

        if (!Tracking::where("sale_id", $ticket->sale->id)->count()) {
            return (new TrackingCodeNotInformed())->calculateScore($ticket);
        } else {
            $trackings = Tracking::where("sale_id", $ticket->sale->id)
                ->where("tracking_status_enum", "!=", Tracking::STATUS_DELIVERED)
                ->get();

            $score = 0;
            foreach ($trackings as $tracking) {
                $trackingDate = Carbon::make($tracking->created_at)->startOfDay();
                $score += $this->calculateTicketScore($trackingDate->diffInDays($ticketDate));
            }

            if (!count($trackings)) {
                return 10;
            }

            return round($score / count($trackings), 2);
        }
    }

    private function calculateTicketScore(int $ellapsedTime)
    {
        $maxScore = 10;
        if ($ellapsedTime <= 30) {
            return $maxScore;
        }

        $score = $maxScore - $ellapsedTime / 6;
        return $score > 0 ? $score : 0;
    }
}
