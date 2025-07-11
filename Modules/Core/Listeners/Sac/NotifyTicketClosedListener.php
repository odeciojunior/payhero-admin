<?php

namespace Modules\Core\Listeners\Sac;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Ticket;
use Modules\Core\Events\Sac\NotifyTicketClosedEvent;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

class NotifyTicketClosedListener implements ShouldQueue
{
    public function handle(NotifyTicketClosedEvent $event)
    {
        try {
            $sendGridService = new SendgridService();

            $ticket = Ticket::select([
                "tickets.id",
                "tickets.sale_id",
                "users.name as owner_name",
                "users.email as owner_email",
            ])
                ->join("sales", "sales.id", "=", "tickets.sale_id")
                ->join("users", "users.id", "=", "sales.owner_id")
                ->where("tickets.id", $event->ticketId)
                ->first();

            if (!empty($ticket)) {
                $nameParts = explode(" ", $ticket->owner_name);
                $firstName = $nameParts[0];

                $data = [
                    "name" => $firstName,
                    "ticket_id" => Hashids::encode($ticket->id),
                    "sale_id" => Hashids::connection("sale_id")->encode($ticket->sale_id),
                ];

                $sendGridService->sendEmail(
                    "noreply@azcend.com.br",
                    "Azcend",
                    $ticket->owner_email,
                    $ticket->owner_name,
                    "d-cae8497d2f434f6797f231407f71ebe4", /// done
                    $data
                );
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
