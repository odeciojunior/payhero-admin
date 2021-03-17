<?php

namespace Modules\Core\Entities\TicketScores;

use Modules\Core\Entities\Ticket;
use Modules\Core\Interfaces\TicketScore;

class DeliveryDelay implements TicketScore
{
    public function calculateScore(Ticket $ticket): int
    {
        return -1;
    }
}
