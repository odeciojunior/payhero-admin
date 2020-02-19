<?php

namespace Modules\Core\Events;

use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\TicketMessage;

class TicketMessageEvent
{
    use SerializesModels;
    public $ticketMessage;

    /**
     * TicketMessagedEvent constructor.
     * @param TicketMessage $ticketMessage
     */
    public function __construct(TicketMessage $ticketMessage)
    {
        $this->ticketMessage = $ticketMessage;
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
