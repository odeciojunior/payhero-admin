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
        $trackings = Tracking::select('product_plan_sale_id', 'tracking_code')
            ->whereNotIn('system_status_enum', [
                Tracking::SYSTEM_STATUS_VALID,
                Tracking::SYSTEM_STATUS_CHECKED_MANUALLY,
            ])
            ->whereIn('tracking_status_enum', [
                Tracking::STATUS_DELIVERED,
                Tracking::STATUS_DISPATCHED,
                Tracking::STATUS_OUT_FOR_DELIVERY
            ])->get();

        $bar = $this->getOutput()->createProgressBar();
        $bar->start($trackings->count());

        $service = new TrackingService();

        foreach ($trackings as $t) {
            $service->createOrUpdateTracking($t->tracking_code, $t->product_plan_sale_id, false, false);
            $bar->advance();
        }

        $bar->finish();
    }
}
