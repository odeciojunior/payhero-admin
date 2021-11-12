<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        //DB::statement('update customers set asaas_buyer_id = null');
    }

}
