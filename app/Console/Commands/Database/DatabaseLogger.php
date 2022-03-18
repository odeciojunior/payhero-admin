<?php

namespace App\Console\Commands\Database;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Cursor;

class DatabaseLogger extends Command
{
    protected $signature = 'database:logger';

    const STATEMENT = 'show full processlist';

    public function handle()
    {
        $today = now('America/Sao_Paulo');
        $startWork = $today::createFromTime(9);
        $endWork = $today::createFromTime(18);
        $isWorkDay = !$today->isSaturday() && !$today->isSunday() && $today->between($startWork, $endWork);

        if($isWorkDay) {
            $results = DB::select(self::STATEMENT);
            $headers = ['User', 'Host', 'Time', 'Info'];
            foreach ($results as $result) {
                if (!is_null($result->Info) && $result->Info != self::STATEMENT) {
                    $data = collect($result)->only($headers);
                    Log::debug("DATABASE\n". json_encode($data, JSON_PRETTY_PRINT));
                }
            }
        }
    }
}
