<?php

namespace Modules\Core\Events\Sac;

class NotifyTicketMediationEvent
{
    public int $ticketId;

    public function __construct(int $ticketId)
    {
        $this->ticketId = $ticketId;
    }
}
