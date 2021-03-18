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
        $saleDate = Carbon::make($ticket->sale->created_at)->startOfDay();
        $ticketDate = Carbon::make($ticket->created_at)->startOfDay();

        if (!Tracking::where('sale_id', $ticket->sale->id)->count()) {
            return (new TrackingCodeNotInformed)->calculateScore($ticket);
        } else {
            Tracking::where('sale_id', $ticket->sale->id)
                ->whereIn('system_status_enum', [
                    Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER,
                    Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE,
                    Tracking::SYSTEM_STATUS_DUPLICATED
                ])->get();
        }

        if ($ticketDate->diffInDays($saleDate) <= 7) {
            return 0;
        }

        return 2;
    }
}
