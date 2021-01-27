<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Transaction;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        try {
            $projects = Transaction::whereIn('gateway_id', [14, 15])
                ->where('is_waiting_withdrawal', 1)
                ->whereNull('withdrawal_id')->get();

            $bar = $this->output->createProgressBar(count($projects));
            $bar->start();
            foreach ($projects as $project) {
                $project->update(['company_id' => 2964]);
                $bar->advance();
            }

            $bar->finish();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
