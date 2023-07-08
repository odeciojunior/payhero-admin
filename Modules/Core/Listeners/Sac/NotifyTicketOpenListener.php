<?php

namespace Modules\Core\Listeners\Sac;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Ticket;
use Modules\Core\Events\Sac\NotifyTicketOpenEvent;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

class NotifyTicketOpenListener implements ShouldQueue
{
    public function handle(NotifyTicketOpenEvent $event)
    {
        try {
            $sendGridService = new SendgridService();

            $ticket = Ticket::select([
                "tickets.sale_id",
                "users.name as owner_name",
                "users.email as owner_email",
                "customers.name as customer_name",
                "user_notifications.ticket_open as notify",
            ])
                ->join("customers", "customers.id", "=", "tickets.customer_id")
                ->join("sales", "sales.id", "=", "tickets.sale_id")
                ->join("users", "users.id", "=", "sales.owner_id")
                ->join("user_notifications", "user_notifications.user_id", "=", "users.id")
                ->where("tickets.id", $event->ticketId)
                ->first();

            if (!empty($ticket) && $ticket->notify) {
                $nameParts = explode(" ", $ticket->owner_name);
                $firstName = $nameParts[0];

                $data = [
                    "first_name" => $firstName,
                    "customer_name" => $ticket->customer_name,
                    "sale_id" => Hashids::connection("sale_id")->encode($ticket->sale_id),
                ];

                $sendGridService->sendEmail(
                    "noreply@nexuspay.com.br",
                    "NexusPay",
                    $ticket->owner_email,
                    $ticket->owner_name,
                    "d-7b7acdd8b3594965a153c0d1746a1452", /// done
                    $data
                );
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
