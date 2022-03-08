<?php

namespace App\Console\Commands;

use Exception;
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
    protected $signature = 'account-health:user:update-average-response-time';

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

            $tickets = Ticket::join('sales', 'sales.id', 'tickets.sale_id')
                ->selectRaw('
                (SUM(tickets.average_response_time) / COUNT(tickets.id)) as average_response_time,
                sales.owner_id'
                )
                ->whereHas('messages', function ($message) {
                    $message->where('type_enum', TicketMessage::TYPE_FROM_ADMIN);
                })
                ->groupBy('sales.owner_id')
                ->orderBy('sales.owner_id');

            foreach ($tickets->cursor() as $ticket) {
                $this->line($ticket->owner_id . ' -- ' . round($ticket->average_response_time) . "h");
                User::find($ticket->owner_id)
                    ->update(['attendance_average_response_time' => round($ticket->average_response_time)]);
            }

        } catch (Exception $e) {
            report($e);
        }

        return 0;
    }
}
