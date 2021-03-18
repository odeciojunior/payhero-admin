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

        if (!Tracking::where('sale_id', $ticket->sale->id)->count()) {
            return (new TrackingCodeNotInformed)->calculateScore($ticket);
        } else {
            $trackings = Tracking::where('sale_id', $ticket->sale->id)
                ->whereIn('system_status_enum', [
                    Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER,
                    Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE,
                    Tracking::SYSTEM_STATUS_DUPLICATED
                ])->orderBy('trackings.created_at', 'DESC')->get();

            $score = 0;
            foreach ($trackings as $tracking) {
                $trackingDate = Carbon::make($tracking->created_at)->startOfDay();
                $score += $this->calculateTicketScore($trackingDate->diffInDays($ticketDate));
            }

            if (!count($trackings)) return 10;

            return round($score / count($trackings), 2);
        }
    }

    private function calculateTicketScore(int $ellapsedTime)
    {
        if ($ellapsedTime <= 5) {
            return 10;
        } else if ($ellapsedTime <= 10) {
            return 15 - $ellapsedTime;
        }
        return 0;
    }
}
