<?php

namespace Modules\Core\Listeners\Sac;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Ticket;
use Modules\Core\Events\Sac\NotifyTicketMediationEvent;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

class NotifyTicketMediationListener implements ShouldQueue
{
    /**
     * @param NotifyTicketMediationEvent $event
     */
    public function handle(NotifyTicketMediationEvent $event)
    {
        try {
            $sendGridService = new SendgridService();

            $ticket = Ticket::select([
                "tickets.id",
                "tickets.sale_id",
                "users.name as owner_name",
                "users.email as owner_email",
                "customers.name as customer_name",
                "customers.name as customer_email",
                "user_notifications.ticket_open as notify",
            ])
                ->join("customers", "customers.id", "=", "tickets.customer_id")
                ->join("sales", "sales.id", "=", "tickets.sale_id")
                ->join("users", "users.id", "=", "sales.owner_id")
                ->join("user_notifications", "user_notifications.user_id", "=", "users.id")
                ->where("tickets.id", $event->ticketId)
                ->first();

            $data = [
                "ticket_id" => Hashids::encode($ticket->id),
                "sale_id" => Hashids::connection("sale_id")->encode($ticket->sale_id),
                "is_customer" => false,
            ];

            $ownerNameParts = explode(" ", $ticket->owner_name);
            $ownerName = $ownerNameParts[0];
            $data["name"] = $ownerName;

            $sendGridService->sendEmail(
                "noreply@cloudox.net",
                "Azcend",
                $ticket->owner_email,
                $ticket->owner_name,
                "d-42e0d5fc42244ca8a8cdbbd37574549e", /// done
                $data
            );

            if ($ticket->customer_email) {
                $customerNameParts = explode(" ", $ticket->customer_name);
                $customerName = $customerNameParts[0];
                $data["name"] = $customerName;
                $data["is_customer"] = true;

                $sendGridService->sendEmail(
                    "noreply@cloudox.net",
                    "CloudFox",
                    $ticket->customer_email,
                    $ticket->customer_name,
                    "d-42e0d5fc42244ca8a8cdbbd37574549e", /// done
                    $data
                );
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
