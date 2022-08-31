<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;

class GenericCommand extends Command
{
    protected $signature = "generic";
    protected $description = "Command description";

    public function handle()
    {
        try {

            $trackingService = new TrackingService();

            $trackings = Tracking::select("product_plan_sale_id", "tracking_code")
                ->where("system_status_enum", Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE)
                ->whereDate("created_at", ">=", now()->subMonths(4));

                $bar = $this->output->createProgressBar($trackings->count());
                $bar->start();

            $i = 0;
            foreach($trackings->cursor() as $key=>$tracking) {
                if($i == 200){
                    $trackingService = new TrackingService();
                    $i = 0;
                }
                $trackingService->createOrUpdateTracking(
                    $tracking->tracking_code,
                    $tracking->product_plan_sale_id,
                );

                $bar->advance();

            }

            $i++;
            $bar->finish();

        } catch (Exception $e) {
            report($e->getMessage());
        }
    }

}
