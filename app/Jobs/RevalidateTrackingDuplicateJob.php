<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Services\TrackingService;

class RevalidateTrackingDuplicateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $trackingCode;

    private array $productPlanSales;

    private TrackingService $trackingService;

    public function __construct(string $trackingCode, array $productPlanSales)
    {
        $this->trackingCode = $trackingCode;
        $this->productPlanSales = $productPlanSales;
        $this->trackingService = new TrackingService();

        $this->allOnQueue("low");
    }

    public function tags()
    {
        return ["revalidate-tracking-duplicate"];
    }

    public function handle()
    {
        $knownInvalidCodes = ["NEXUSPAY000XX", "ALIEXPRESS"];
        if (!in_array($this->trackingCode, $knownInvalidCodes)) {
            foreach ($this->productPlanSales as $productPlanSale) {
                $this->trackingService->createOrUpdateTracking(
                    $this->trackingCode,
                    $productPlanSale["id"],
                    false,
                    false,
                    false
                );
            }
        }
    }
}
