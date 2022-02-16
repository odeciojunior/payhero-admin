<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Ticket;
use Modules\Core\Services\AttendanceService;

class UpdateAttendanceAverageTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account-health:tickets:update-average-response-time';

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

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {
            $attendanceService = new AttendanceService();
            Ticket::with('messages')
                ->chunk(500, function ($tickets) use ($attendanceService) {
                    foreach ($tickets as $ticket) {
                        $averageResponseTime = $attendanceService->getTicketAverageResponseTime($ticket);
                        $this->line($ticket->id . ' -- ' . $averageResponseTime . "h");
                        $ticket->update(['average_response_time' => $averageResponseTime]);
                    }
                });
        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));
        return 0;
    }
}
