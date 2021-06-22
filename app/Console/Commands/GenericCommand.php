<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\MelhorenvioIntegration;
use Modules\Core\Services\MelhorenvioService;
use Modules\Core\Services\TrackingmoreService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
      $t = new TrackingmoreService();

      $a = $t->find('LB099631813HK');

      dd($a);
    }
}



