<?php

namespace App\Console\Commands;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Console\Command;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        //
    }

}
