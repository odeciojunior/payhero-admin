<?php

namespace App\Console\Commands\Sac;

use Illuminate\Console\Command;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Events\Sac\NotifyTicketMediationEvent;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\SmsService;

class SetMediation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:mediation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Coloca os chamados em mediação automáticamente após';

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
        $daysWithoutUserResponse = 5;

        $query = Ticket::where('ticket_status_enum', Ticket::STATUS_OPEN)
            ->where('ticket_category_enum', Ticket::CATEGORY_COMPLAINT)
            ->where('last_message_date', '<', now()->subDays($daysWithoutUserResponse))
            ->where('last_message_type_enum', TicketMessage::TYPE_FROM_CUSTOMER);

        $bar = $this->getOutput()->createProgressBar($query->count());
        $bar->start();

        $query->chunk(500, function ($tickets) use ($bar) {
            foreach ($tickets as $ticket) {
                try {

                    $ticket->ticket_status_enum = Ticket::STATUS_MEDIATION;
                    $ticket->save();

                    event(new NotifyTicketMediationEvent($ticket->id));

                } catch (\Exception $e) {
                    report($e);
                }
                $bar->advance();
            }
        });

        $bar->finish();
    }
}
