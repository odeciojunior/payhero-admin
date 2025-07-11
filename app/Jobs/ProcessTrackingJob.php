<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;

class ProcessTrackingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Tracking $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tracking $tracking)
    {
        $this->tracking = $tracking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $trackingService = new TrackingService();
        $trackingService->createOrUpdateTracking(
            $this->tracking->tracking_code,
            $this->tracking->product_plan_sale_id,
        );

    }
}
