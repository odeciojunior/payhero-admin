<?php

namespace App\Console\Commands\Sac;

use Illuminate\Console\Command;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;

class CloseRefundedSalesTickets extends Command
{
    protected $signature = "verify:tickets-refunded";

    protected $description = "Automatically closes refunded sales tickets";

    public function handle()
    {
        $query = Ticket::select("tickets.id", "tickets.ticket_status_enum")
            ->join("sales", "sales.id", "=", "tickets.sale_id")
            ->whereIn("tickets.ticket_status_enum", [Ticket::STATUS_OPEN, Ticket::STATUS_MEDIATION])
            ->whereIn("sales.status", [
                Sale::STATUS_REFUNDED,
                Sale::STATUS_BILLET_REFUNDED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_CANCELED_ANTIFRAUD,
            ]);

        $bar = $this->getOutput()->createProgressBar();
        $bar->start($query->count());

        $query->chunk(1000, function ($tickets) use ($bar) {
            foreach ($tickets as $ticket) {
                $ticket->ticket_status_enum = Ticket::STATUS_CLOSED;
                $tickets->mediation_notified = 0;
                $ticket->save();

                BlockReasonSale::where("sale_id", $ticket->sale_id)
                    ->where("status", BlockReasonSale::STATUS_BLOCKED)
                    ->where("blocked_reason_id", 8)
                    ->update([
                        "status" => BlockReasonSale::STATUS_UNLOCKED,
                    ]);

                $bar->advance();
            }
        });

        $bar->finish();
    }
}
