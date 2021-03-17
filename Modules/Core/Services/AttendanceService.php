<?php

namespace Modules\Core\Services;

use Illuminate\Support\Carbon;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Entities\User;

class AttendanceService
{
    public function getCurrentUnsolvedTicketsRate(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        //40 dias
        //abertos como reclamação
        //validar cada registro de tracking contra a data da venda
        //atraso = data da venda - data do chamado
        return 0;
    }

    public function getTicketsPerSaleRate(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        return 0;
    }

    public function getComplainTicketsPerApprovedSaleRate(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        return 0;
    }

    public function getUnsolvedTicketsRate(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        return 0;
    }

    public function getSolvedTicketsRate(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        return 0;
    }

    public function getComplaintTicketsInPeriod(User $user, Carbon $startDate, Carbon $endDate)
    {
        return Ticket::select('tickets.*')
            ->join('sales', 'sales.id', 'tickets.sale_id')
            ->where('sales.owner_id', $user->id)
            ->whereNotNull('subject_enum')
            ->where('ticket_category_enum', Ticket::CATEGORY_COMPLAINT)
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->get();
    }

    public function getAverageResponseTimeInDays(User $user): ?float
    {
        $ticketMesageModel = new TicketMessage();
        $tickets = Ticket::join('sales', 'sales.id', 'tickets.sale_id')
            ->selectRaw('
                (SUM(tickets.average_response_time) / COUNT(tickets.id)) as average_response_time,
                sales.owner_id'
            )
            ->where('sales.owner_id', $user->id)
            ->whereHas('messages', function ($message) use ($ticketMesageModel) {
                $message->where('type_enum', $ticketMesageModel->present()->getType('from_admin'));
            })->groupBy('sales.owner_id');

        $averageResponseTime = $tickets->get()[0]['average_response_time'];

        return round($averageResponseTime / 24, 2) ?? null;
    }
}
