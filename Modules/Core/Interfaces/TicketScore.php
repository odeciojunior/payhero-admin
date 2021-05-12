<?php

namespace Modules\Core\Interfaces;

use Modules\Core\Entities\Ticket;

interface TicketScore
{
    public function calculateScore(Ticket $ticket): int;
}
