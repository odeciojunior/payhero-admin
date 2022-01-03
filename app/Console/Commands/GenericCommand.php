<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\GatewayPostback;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        
    }

}
