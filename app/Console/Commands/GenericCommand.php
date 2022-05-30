<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Modules\Core\Services\DemoFakeDataService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        Config::set('database.default', 'demo');

        $demo = new DemoFakeDataService();
        $demo->createAffiliates();
    }
}
