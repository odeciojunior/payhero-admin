<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;

class ProcessTrackingmorePostbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $trackingCode;

    private TrackingService $trackingService;

    public function __construct(string $trackingCode)
    {
        $this->trackingCode = $trackingCode;
        $this->trackingService = new TrackingService();

        $this->allOnQueue("postback");
    }

    public function tags()
    {
        return ["process-trackingmore-postback"];
    }

    public function handle()
    {
        $trackingCode = $this->trackingCode;

        $trackings = Tracking::select("product_plan_sale_id")
            ->where("tracking_code", $trackingCode)
            ->get();

        foreach ($trackings as $tracking) {
            $this->trackingService->createOrUpdateTracking(
                $trackingCode,
                $tracking->product_plan_sale_id,
                false,
                false
            );
        }
    }
}
