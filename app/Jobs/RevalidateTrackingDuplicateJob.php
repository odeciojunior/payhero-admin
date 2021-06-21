<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Modules\Core\Services\TrackingService;

class RevalidateTrackingDuplicateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $trackingCode;

    private Collection $productPlanSales;

    private TrackingService $trackingService;

    public function __construct(string $trackingCode, Collection $productPlanSales)
    {
        $this->trackingCode = $trackingCode;
        $this->productPlanSales = $productPlanSales;
        $this->trackingService = new TrackingService();
    }

    public function tags()
    {
        return ['revalidate-tracking-duplicate'];
    }

    public function handle()
    {
        $first = $this->productPlanSales->first();
        $this->productPlanSales->shift();
        $revalidatedTracking = $this->trackingService->createOrUpdateTracking($this->trackingCode, $first, false, false);
        if ($revalidatedTracking && $this->productPlanSales->isNotEmpty()) {
            foreach ($this->productPlanSales as $productPlanSale) {
                $tracking = $productPlanSale->tracking;
                $tracking->system_status_enum = $revalidatedTracking->system_status_enum;
                $tracking->tracking_status_enum = $revalidatedTracking->tracking_status_enum;
                if ($tracking->isDirty()) {
                    $tracking->save();
                }
            }
        }
    }
}
