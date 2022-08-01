<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketAttachment;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;

class GenericCommand extends Command
{
    protected $signature = 'generic';
    protected $description = 'Command description';

    public function handle()
    {
        $tickets = Ticket::select('id', 'sale_id')
            ->where('description', 'Chamado criado automáticamente para venda a mais de 15 dias sem código de rastreio')
            ->get();

        $bar = $this->getOutput()->createProgressBar();
        $bar->start($tickets->count());

        foreach ($tickets as $t) {
            TicketMessage::where('ticket_id', $t->id)->forceDelete();
            TicketAttachment::where('ticket_id', $t->id)->forceDelete();
            BlockReasonSale::where('sale_id', $t->sale_id)
                ->where('blocked_reason_id', 8)
                ->forceDelete();
            $t->forceDelete();
           $bar->advance();
        }

        $bar->finish();
    }
}
