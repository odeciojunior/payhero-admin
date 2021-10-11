<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UnicodropIntegration;
use Modules\Core\Services\UnicodropService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $unicodropIntegration = UnicodropIntegration::find(3);

        $unicoDropService = new UnicodropService($unicodropIntegration);

        $unicoDropService->boletoPaid(Sale::find(1219416));
        $unicoDropService->pixExpired(Sale::find(1222164));
    }
}
