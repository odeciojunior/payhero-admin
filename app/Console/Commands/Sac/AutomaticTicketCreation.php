<?php

namespace App\Console\Commands\Sac;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Events\Sac\NotifyTicketOpenEvent;

class AutomaticTicketCreation extends Command
{
    protected $signature = 'create:ticket';

    protected $description = 'Cria chamados automaticamente para vendas sem rastreamento';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $daysWithoutTracking = 15;

        Sale::select([
            'sales.id',
            'sales.customer_id'
        ])->leftJoin('trackings', 'trackings.sale_id', '=', 'sales.id')
            ->leftJoin('tickets', 'tickets.sale_id', '=', 'sales.id')
            ->where('sales.status', Sale::STATUS_APPROVED)
            ->where('start_date', '>=', now()->subDays($daysWithoutTracking))
            ->whereNull('trackings.id')
            ->whereNull('tickets.id')
            ->chunk(500, function ($sales) use ($daysWithoutTracking) {
                foreach ($sales as $sale) {
                    try {
                        $ticket = Ticket::create([
                            'sale_id' => $sale->id,
                            'customer_id' => $sale->customer_id,
                            'subject' => 'Código de rastreio não informado',
                            'subject_enum' => Ticket::SUBJECT_TRACKING_CODE_NOT_RECEIVED,
                            'description' => "Chamado criado automáticamente para venda a mais de {$daysWithoutTracking} dias sem código de rastreio",
                            'ticket_category_enum' => Ticket::CATEGORY_COMPLAINT,
                            'ticket_status_enum' => Ticket::STATUS_OPEN,
                            'mediation_notified' => 0,
                        ]);
                        event((new NotifyTicketOpenEvent($ticket->id)));
                    } catch (\Exception $e) {
                        report($e);
                    }
                }
            });
    }
}
