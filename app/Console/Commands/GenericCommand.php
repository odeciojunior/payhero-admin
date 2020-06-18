<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generic';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $trackingModel = new Tracking();
        $trackingService = new TrackingService();

        $count = 0;
        $trackingModel->with('productPlanSale')
            ->where('system_status_enum', $trackingModel->present()->getSystemStatusEnum('unknown_carrier'))
            ->chunk(100, function ($trackings) use ($trackingService, &$count) {
                foreach ($trackings as $tracking) {
                    try {
                        $count++;
                        $this->line("tracking: {$count}");
                        $trackingCode = $tracking->tracking_code;
                        $pps = $tracking->productPlanSale;
                        $trackingService->createOrUpdateTracking($trackingCode, $pps, false, true);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            });

        return;
    }
}


