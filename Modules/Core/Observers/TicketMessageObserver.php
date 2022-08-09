<?php

namespace Modules\Core\Observers;

use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;

class TicketMessageObserver
{
    private function updateTicket(TicketMessage $ticketMessage)
    {
        Ticket::where("id", $ticketMessage->ticket_id)->update([
            "last_message_type_enum" => $ticketMessage->type_enum,
            "last_message_date" => $ticketMessage->created_at,
        ]);
    }

    public function created(TicketMessage $ticketMessage)
    {
        $this->updateTicket($ticketMessage);
    }

    public function updated(TicketMessage $ticketMessage)
    {
        $this->updateTicket($ticketMessage);
    }
}
