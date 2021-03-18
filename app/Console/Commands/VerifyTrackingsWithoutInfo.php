<?php

namespace App\Console\Commands;

use App\Exceptions\CommandMonitorTimeException;
use Illuminate\Console\Command;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;

class VerifyTrackingsWithoutInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:trackingWithoutInfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $start = now();

        $trackingModel = new Tracking();
        $trackingService = new TrackingService();

        $trackingModel->with('productPlanSale')
        ->whereIn('system_status_enum', [
            $trackingModel->present()->getSystemStatusEnum('no_tracking_info'),
            $trackingModel->present()->getSystemStatusEnum('unknown_carrier')
        ])->chunk(100, function ($trackings) use ($trackingService) {
                foreach ($trackings as $tracking) {
                    try {
                        $trackingCode = $tracking->tracking_code;
                        $pps = $tracking->productPlanSale;
                        $trackingService->createOrUpdateTracking($trackingCode, $pps);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            });

        $end = now();
        report(new CommandMonitorTimeException("command {$this->signature} começou as {$start} e terminou as {$end}"));

        return;
    }
}
