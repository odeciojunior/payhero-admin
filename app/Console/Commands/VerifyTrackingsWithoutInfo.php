<?php

namespace App\Console\Commands;

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

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function handle()
    {
        $trackingModel = new Tracking();
        $trackingService = new TrackingService();

        $trackingModel->with('productPlanSale')
        ->where('system_status_enum', $trackingModel->present()->getSystemStatusEnum('no_tracking_info'))
            ->chunk(100, function ($trackings) use ($trackingService) {
                foreach ($trackings as $tracking) {
                    try {
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
