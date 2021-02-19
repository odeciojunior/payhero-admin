<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;
use Carbon\Carbon;

class UpdateAttendanceAverageTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-attendance-average-ticket';

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
            
            $tickets = Ticket::with('messages')->get();

            $ticketMesagePresenter = (new TicketMessage)->present();

            foreach ($tickets as $ticket) {
                
                $average = 0;
                $lastCustomer = '';
                $lastProducer = '';
                $firstProducer = '';

                foreach ($ticket->messages as $key => $message) {
                    
                    if($message->type_enum == $ticketMesagePresenter->getType('from_customer')) {
                        $lastCustomer = $message;
                    }

                    if($message->type_enum == $ticketMesagePresenter->getType('from_admin')) {
                        $lastProducer = $message;

                        if(empty($firstProducer)) {
                            $firstProducer = $message;
                            if(!empty($lastCustomer))
                                $average = Carbon::parse($lastProducer->created_at)->diffInHours($lastCustomer->created_at);
                            else
                                $average = Carbon::parse($lastProducer->created_at)->diffInHours($ticket->created_at);
                        } else {
                            if(!empty($lastCustomer)) {
                                $average = ($average + Carbon::parse($lastProducer->created_at)->diffInHours($lastCustomer->created_at)) / 2;
                            } else{
                                $average = ($average + Carbon::parse($lastProducer->created_at)->diffInHours($ticket->created_at)) / 2;
                            }
                        }
                    }

                }

                $this->info($ticket->id . ' -- ' . $average);
                $ticket->update(['average_response_time' => $average]);
            }

        } catch (Exception $e) {
            report($e);
        }
    }
}
