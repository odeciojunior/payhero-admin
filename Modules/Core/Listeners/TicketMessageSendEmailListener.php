<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Events\TicketMessageEvent;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SendgridService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class TrackingCodeUpdatedSendEmailClientListener
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
            $projectModel    = new Project();
            $domainModel     = new Domain();
            $ticketMessage   = $event->ticketMessage->load(['ticket', 'ticket.customer', 'ticket.sale']);
            $customer        = $ticketMessage->ticket->customer;
            $sale            = $ticketMessage->ticket->sale;
            $project         = $projectModel->find($sale->project_id);
            $domain          = $domainModel->where('project_id', $project->id)->where('status', 3)->first();
            $customerName    = explode(' ', $customer->name);
            $data            = [
                'first_name'      => $customerName[0],
                'date'            => $ticketMessage->created_at->format('d/m/Y H:i:s'),
                'project_name'    => $project->name,
                'store_logo'      => $project->logo,
                "project_contact" => $project->contact,
                'ticket_code'     => Hashids::encode($ticketMessage->ticket->id),
            ];
            dd($data);

            $sendGridService->sendEmail('noreply@' . $domain->name, $project->name, $customer->email, $customer->name, 'd-4ce62be1218d4b258c8d1ab139d4d664', $data);
        } catch (Exception $e) {
            Log::warning('Erro ao enviar email de mensagem de chamado');
            report($e);
        }
    }

    public function tags()
    {
        return ['listener:' . static::class, 'tracking'];
    }
}
