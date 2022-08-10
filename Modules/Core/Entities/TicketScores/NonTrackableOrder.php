<?php

namespace Modules\Core\Entities\TicketScores;

use Carbon\Carbon;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Tracking;
use Modules\Core\Interfaces\TicketScore;

class NonTrackableOrder implements TicketScore
{
    public function calculateScore(Ticket $ticket): int
    {
        $ticketDate = Carbon::make($ticket->created_at)->startOfDay();

        if (!Tracking::where("sale_id", $ticket->sale->id)->count()) {
            return (new TrackingCodeNotInformed())->calculateScore($ticket);
        } else {
            $trackings = Tracking::where("sale_id", $ticket->sale->id)
                ->orderBy("trackings.created_at", "DESC")
                ->get();

            $score = 0;
            foreach ($trackings as $tracking) {
                $trackingDate = Carbon::make($tracking->created_at)->startOfDay();
                $score += $this->calculateTicketScore($trackingDate->diffInDays($ticketDate));
            }

            return round($score / count($trackings), 2);
        }
    }

    private function calculateTicketScore(int $ellapsedTime)
    {
        if ($ellapsedTime <= 5) {
            return 10;
        } elseif ($ellapsedTime <= 10) {
            return 5;
        }
        return 0;
    }
}
