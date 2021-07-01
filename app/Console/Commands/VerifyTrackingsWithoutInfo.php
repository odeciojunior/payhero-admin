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

    public function handle()
    {
        $trackingModel = new Tracking();
        $trackingService = new TrackingService();

        $query = $trackingModel->with('productPlanSale')
            ->whereIn('system_status_enum', [
                Tracking::SYSTEM_STATUS_NO_TRACKING_INFO,
                Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER,
            ])->whereDate('created_at', '>=', now()->subMonths(4));

        $total = $query->count();
        $count = 0;

        $query->chunk(100, function ($trackings) use ($total, &$count, $trackingService) {
            foreach ($trackings as $tracking) {
                try {
                    $count++;
                    $this->line("T {$count} de {$total}: {$tracking->tracking_code}");
                    $trackingCode = $tracking->tracking_code;
                    $pps = $tracking->productPlanSale;
                    $trackingService->createOrUpdateTracking($trackingCode, $pps->id);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                    continue;
                }
            }
        });
    }
}
