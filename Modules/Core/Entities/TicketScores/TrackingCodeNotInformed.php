<?php

namespace Modules\Core\Entities\TicketScores;

use Carbon\Carbon;
use Modules\Core\Entities\Ticket;
use Modules\Core\Interfaces\TicketScore;

class TrackingCodeNotInformed implements TicketScore
{
    public function calculateScore(Ticket $ticket): int
    {
        $saleDate = Carbon::make($ticket->sale->created_at)->startOfDay();
        $ticketDate = Carbon::make($ticket->created_at)->startOfDay();

        if ($ticketDate->diffInDays($saleDate) <= 7) {
            return 7;
        }

        return 0;
    }
}
