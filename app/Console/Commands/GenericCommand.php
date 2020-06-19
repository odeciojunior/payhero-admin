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

        $trackingsQuery = $trackingModel->with('productPlanSale')
            ->where('system_status_enum', $trackingModel->present()->getSystemStatusEnum('unknown_carrier'));

        $userId = $this->argument('user');
        if (!empty($userId)) {
            $trackingsQuery->whereHas('sale', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            });
        }

        $count = 0;
        $trackingsQuery->chunk(100, function ($trackings) use ($trackingService, &$count) {
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


