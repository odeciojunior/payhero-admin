<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Benefit;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Services\TrackingService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        try {

            $user = User::with('benefits')->find(557)->benefits->toArray();
            dd($user);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
