<?php

namespace Modules\Core\Events\Sac;

class NotifyTicketOpenEvent
{
    public int $ticketId;

    public function __construct(int $ticketId)
    {
        $this->ticketId = $ticketId;
    }
}
