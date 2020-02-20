<?php

namespace Modules\Core\Events;

use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\TicketMessage;

class TicketMessageEvent
{
    use SerializesModels;
    public $ticketMessage;
    public $lastAdminMessage;

    /**
     * TicketMessageEvent constructor.
     * @param TicketMessage $ticketMessage
     * @param TicketMessage $lastAdminMessage
     */
    public function __construct(TicketMessage $ticketMessage, $lastAdminMessage = null)
    {
        $this->ticketMessage    = $ticketMessage;
        $this->lastAdminMessage = $lastAdminMessage;
    }

    /**
     * Get the channels the event should be broadcast on.
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
