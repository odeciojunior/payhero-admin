<?php

namespace Modules\Core\Listeners;

use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\TicketMessage;
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
            $sendGridService      = new SendgridService();
            $projectModel         = new Project();
            $domainModel          = new Domain();
            $ticketMessage        = $event->ticketMessage->load(['ticket', 'ticket.customer', 'ticket.sale']);
            $lastAdminMessage     = $event->lastAdminMessage;
            $lastAdminMessageTime = !empty($lastAdminMessage) ? Carbon::parse($lastAdminMessage->created_at)
                                                                      ->addHour() : Carbon::parse($ticketMessage->created_at)
                                                                                          ->subHour();

            if ($ticketMessage->created_at >= $lastAdminMessageTime) {
                $customer = $ticketMessage->ticket->customer;
                $sale     = $ticketMessage->ticket->sale;
                $project  = $projectModel->find($sale->project_id);
                $domain   = $domainModel->where('project_id', $project->id)->where('status', 3)->first();
                if (!empty($domain)) {

                    $customerName = explode(' ', $customer->name);
                    $data         = [
                        'first_name'      => $customerName[0],
                        'date'            => $ticketMessage->created_at->format('d/m/Y H:i:s'),
                        'project_name'    => $project->name,
                        'project_logo'    => $project->logo,
                        "project_contact" => $project->contact,
                        'ticket_code'     => Hashids::encode($ticketMessage->ticket->id),
                        'link'            => "https://sac." . $domain->name,
                    ];

                    $sendGridService->sendEmail('noreply@' . $domain->name, $project->name, $customer->email, $customer->name, 'd-e44117575a044be193c2f7235bc22789', $data);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao enviar email de mensagem de chamado ao cliente');
            report($e);
        }
    }

    public function tags()
    {
        return ['listener:' . static::class, 'tracking'];
    }
}
