<?php

namespace App\Console\Commands\Sac;

use Illuminate\Console\Command;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;

class VerifyTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Encerra automaticamente os chamados que nao tiveram resposta dos usuarios em determinado periodo de tempo";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $daysWithoutUserResponse = 3;
        $systemMessage = "Não houve interação sua nos últimos {$daysWithoutUserResponse} dias, por isso, entendemos que o problema já foi resolvido e encerramos o chamado automaticamente. Você pode reabri-lo sempre que preciso.";

        $query = Ticket::where('ticket_status_enum', Ticket::STATUS_OPEN)
            ->where('last_message_date', '<', now()->subDays($daysWithoutUserResponse))
            ->where('last_message_type_enum', TicketMessage::TYPE_FROM_ADMIN);

        $bar = $this->getOutput()->createProgressBar($query->count());
        $bar->start();

        $query->chunk(500, function ($tickets) use ($bar, $systemMessage) {

            foreach ($tickets as $ticket) {
                try {
                    TicketMessage::updateOrCreate([
                        'ticket_id' => $ticket->id,
                        'type_enum' => TicketMessage::TYPE_FROM_SYSTEM,
                    ], [
                        'message' => $systemMessage,
                        'created_at' => now()->toDateTimeString()
                    ]);

                    $ticket->ticket_status_enum = Ticket::STATUS_CLOSED;
                    $tickets->mediation_notified = 0;
                    $ticket->save();

                    BlockReasonSale::where('sale_id', $ticket->sale_id)
                        ->where('status', BlockReasonSale::STATUS_BLOCKED)
                        ->where('blocked_reason_id', 8)
                        ->update([
                            'status' => BlockReasonSale::STATUS_UNLOCKED
                        ]);
                } catch (\Exception $e) {
                    report($e);
                }
                $bar->advance();
            }
        });

        $bar->finish();
    }
}
