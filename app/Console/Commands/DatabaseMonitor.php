<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Cursor;

class DatabaseMonitor extends Command
{
    protected $signature = 'database:monitor';

    public function handle()
    {
        $cursor = new Cursor($this->output);
        while (true) {
            $results = DB::select('show processlist');

            $headers = ['User', 'Host', 'Time', 'Info'];
            $data = [];
            foreach ($results as $result) {
                if(!is_null($result->Info)) {
                    $data[] = collect($result)
                        ->only($headers)
                        ->values()
                        ->toArray();
                }
            }

            $cursor->moveToPosition(0, 0);
            $cursor->clearOutput();

            $this->table($headers, $data);
            $this->line("\nDigite <fg=green>Ctrl+C<fg=default> para sair.");

            sleep(1);
        }
    }
}
