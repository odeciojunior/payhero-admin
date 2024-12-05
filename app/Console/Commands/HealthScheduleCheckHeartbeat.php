<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;

class HealthScheduleCheckHeartbeat extends Command
{
    protected $signature = 'health:schedule-check-heartbeat';
    protected $description = 'Checks the heartbeat of the health schedule.';

    public function handle()
    {
        // Implementação do comando
        $this->info('Comando executado com sucesso!');
    }
}
