<?php

namespace Modules\Core\Listeners\Sac;

use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Events\Sac\TicketMessageEvent;
use Modules\Core\Services\SendgridService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class TicketMessageSendEmailListener
 * @package Modules\Core\Listeners
 */
class TicketMessageSendEmailListener implements ShouldQueue
{
    use Queueable;

    /**
     * @param TicketMessageEvent $event
     */
    public function handle(TicketMessageEvent $event)
    {
        try {
            $sendGridService = new SendgridService();
            $ticketMessage = $event->ticketMessage->load(["ticket", "ticket.customer", "ticket.sale"]);
            $lastAdminMessage = $event->lastAdminMessage;
            $lastAdminMessageTime = !empty($lastAdminMessage)
                ? Carbon::parse($lastAdminMessage->created_at)->addHour()
                : Carbon::parse($ticketMessage->created_at)->subHour();

            if ($ticketMessage->created_at >= $lastAdminMessageTime) {
                $customer = $ticketMessage->ticket->customer;
                $sale = $ticketMessage->ticket->sale;
                $project = Project::find($sale->project_id);
                $domain = Domain::where("project_id", $project->id)
                    ->where("status", 3)
                    ->first();

                $customerName = explode(" ", $customer->name);
                $data = [
                    "first_name" => $customerName[0],
                    "date" => $ticketMessage->created_at->format("d/m/Y H:i:s"),
                    "project_name" => $project->name,
                    "project_logo" => $project->checkoutConfig->checkout_logo,
                    "ticket_code" => Hashids::encode($ticketMessage->ticket->id),
                ];

                $fromEmail = "noreply@" . ($domain ? $domain->name : "azcend.com.br");
                $sendGridService->sendEmail(
                    $fromEmail,
                    $project->name,
                    $customer->email,
                    $customer->name,
                    "d-3b639852468c4e90b56fa953c5ca0303", /// done
                    $data
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao enviar email de mensagem de chamado ao cliente");
            report($e);
        }
    }

    public function tags()
    {
        return ["listener:" . static::class];
    }
}
