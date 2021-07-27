<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $service = new TrackingService();

        $trackings = DB::select("select tracking_code, product_plan_sale_id
                                          from trackings
                                          where tracking_code in (
                                              select tracking_code
                                              from trackings
                                              where system_status_enum = 5
                                          )");

        $bar = $this->output->createProgressBar($trackings->count());
        $bar->start();

        foreach ($trackings as $t) {
            $service->createOrUpdateTracking($t->tracking_code, $t->product_plan_sale_id, false, false);
            $bar->advance();
        }

        $bar->finish();
    }
}


