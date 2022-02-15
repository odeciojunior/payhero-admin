<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;
use Illuminate\Support\Facades\Log;

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

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

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

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
