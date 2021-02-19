<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Entities\User;

class UpdateAttendanceAverageUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-attendance-average-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        try {
            
            $ticketMesageModel = new TicketMessage;

            $tickets = Ticket::join('sales', 'sales.id', 'tickets.sale_id')
            ->selectRaw('
                (SUM(tickets.average_response_time) / COUNT(tickets.id)) as average_response_time,
                sales.owner_id'
            )
            ->whereHas('messages', function($message) use ($ticketMesageModel) {
                $message->where('type_enum', $ticketMesageModel->present()->getType('from_admin'));
            })
            ->groupBy('sales.owner_id');

            foreach ($tickets->cursor() as $ticket) {
                User::find($ticket->owner_id)
                    ->update(['attendance_average_response_time' => $ticket->average_response_time]);
            }

        } catch (Exception $e) {
            report($e);
        }
    }
}
