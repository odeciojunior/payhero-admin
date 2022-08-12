<?php

namespace Modules\Core\Services;

use Illuminate\Support\Carbon;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Entities\User;

class AttendanceService
{
    public function getTicketsPerApprovedSaleRate(User $user, Carbon $startDate, Carbon $endDate): float
    {
        $saleService = new SaleService();
        $approvedSalesCount = $saleService->getApprovedSalesInPeriod($user, $startDate, $endDate)->count();

        $ticketsCount = Ticket::join("sales", "sales.id", "tickets.sale_id")
            ->where("sales.owner_id", $user->id)
            ->whereNotNull("subject_enum")
            ->where("ticket_category_enum", Ticket::CATEGORY_COMPLAINT)
            ->whereBetween("sales.start_date", [
                $startDate->format("Y-m-d") . " 00:00:00",
                $endDate->format("Y-m-d") . " 23:59:59",
            ])
            ->count();

        if (!$approvedSalesCount) {
            return 0;
        }

        return round(($ticketsCount / $approvedSalesCount) * 100, 2);
    }

    public function getComplaintTicketsInPeriod(User $user, Carbon $startDate, Carbon $endDate)
    {
        return Ticket::select("tickets.*")
            ->join("sales", "sales.id", "tickets.sale_id")
            ->where("sales.owner_id", $user->id)
            ->whereNotNull("subject_enum")
            ->where("ticket_category_enum", Ticket::CATEGORY_COMPLAINT)
            ->whereBetween("tickets.created_at", [$startDate, $endDate])
            ->get();
    }

    public function getAverageResponseTimeInDays(User $user): ?float
    {
        $ticketMesageModel = new TicketMessage();
        $tickets = Ticket::join("sales", "sales.id", "tickets.sale_id")
            ->selectRaw(
                '
                (SUM(tickets.average_response_time) / COUNT(tickets.id)) as average_response_time,
                sales.owner_id'
            )
            ->where("sales.owner_id", $user->id)
            ->whereHas("messages", function ($message) use ($ticketMesageModel) {
                $message->where("type_enum", $ticketMesageModel->present()->getType("from_admin"));
            })
            ->groupBy("sales.owner_id");

        $averageResponseTime = isset($tickets->get()[0]) ? $tickets->get()[0]["average_response_time"] : 0;

        return round($averageResponseTime / 24, 2) ?? null;
    }

    public function getTicketAverageResponseTime(Ticket $ticket): float
    {
        /** we start creating an array of replies[type, date], assuming the first interaction is the creation of ticket by customer */
        $replies[] = ["type" => TicketMessage::TYPE_FROM_CUSTOMER, "date" => $ticket->created_at];
        $lastMessageType = TicketMessage::TYPE_FROM_CUSTOMER;

        /** It iterates over all messages and add to $replies only the next of different type, excluding system messages */
        foreach ($ticket->messages as $key => $message) {
            if ($message->type_enum != $lastMessageType && $message->type_enum != TicketMessage::TYPE_FROM_SYSTEM) {
                $replies[] = ["type" => $message->type_enum, "date" => $message->created_at];
                $lastMessageType = $message->type_enum;
            }
        }

        /** If the last message is from customer, it indicates that seller didn't answer yet,
         * so this interaction is virtually conditioned by Ticket status, keeping our customer/seller answer pairs */
        if ($replies[array_key_last($replies)]["type"] == TicketMessage::TYPE_FROM_CUSTOMER) {
            if ($ticket->ticket_status_enum == Ticket::STATUS_OPEN) {
                $responseDate = now();
            } elseif ($ticket->ticket_status_enum == Ticket::STATUS_CLOSED) {
                $responseDate = $replies[array_key_last($replies)]["date"];
            } elseif ($ticket->ticket_status_enum == Ticket::STATUS_MEDIATION) {
                $responseDate = Carbon::parse($replies[array_key_last($replies)]["date"])->addDays(7);
            }
            $replies[] = ["type" => TicketMessage::TYPE_FROM_ADMIN, "date" => $responseDate];
        }

        $totalEllapsedTime = 0;
        $repliesCount = 0;
        foreach ($replies as $key => $reply) {
            /** Our array is made by a customer message forwarded by a seller message, again and again,
             * setting virtual pairs, so we can subtract current key date (customer) from the next key date (seller)
             * without array index issues */
            if ($reply["type"] == TicketMessage::TYPE_FROM_CUSTOMER) {
                $totalEllapsedTime += Carbon::parse($replies[$key + 1]["date"])->diffInMinutes($replies[$key]["date"]);
                $repliesCount++;
            }
        }

        return round($totalEllapsedTime / 60 / $repliesCount);
    }
}
