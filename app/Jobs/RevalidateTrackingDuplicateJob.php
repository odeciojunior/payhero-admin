<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Services\TrackingService;

class RevalidateTrackingDuplicateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $trackingCode;

    private ProductPlanSale $productPlanSale;

    private TrackingService $trackingService;

    public function __construct(string $trackingCode, ProductPlanSale $productPlanSale)
    {
        $this->trackingCode = $trackingCode;
        $this->productPlanSale = $productPlanSale;
        $this->trackingService = new TrackingService();
    }

    public function handle()
    {
        $this->trackingService->createOrUpdateTracking($this->trackingCode, $this->productPlanSale, false, false);
    }
}
