<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $service = new TrackingService();

        $trackings = Tracking::select('trackings.product_plan_sale_id', 'trackings.tracking_code')
            ->join('sales', 'sales.id', '=', 'trackings.sale_id')
            ->where('sales.owner_id', 4125)
            ->get();

        $bar = $this->output->createProgressBar($trackings->count());
        $bar->start();

        foreach ($trackings as $t) {
            $service->createOrUpdateTracking($t->tracking_code, $t->product_plan_sale_id, false, false);
            $bar->advance();
        }

        $bar->finish();
    }
}


