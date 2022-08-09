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
    protected $signature = "verify:trackingWithoutInfo";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

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
        $trackingService = new TrackingService();

        $query = Tracking::select("product_plan_sale_id", "tracking_code")
            ->whereIn("system_status_enum", [
                Tracking::SYSTEM_STATUS_NO_TRACKING_INFO,
                Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER,
            ])
            ->whereDate("created_at", ">=", now()->subMonths(4));

        $bar = $this->getOutput()->createProgressBar($query->count());
        $bar->start();

        $query->chunk(100, function ($trackings) use ($bar, $trackingService) {
            foreach ($trackings as $t) {
                try {
                    $trackingService->createOrUpdateTracking($t->tracking_code, $t->product_plan_sale_id);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                    continue;
                } finally {
                    $bar->advance();
                }
            }
        });

        $bar->finish();
    }
}
