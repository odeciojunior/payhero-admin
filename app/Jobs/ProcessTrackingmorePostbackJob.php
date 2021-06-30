<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\ProductPlanSale;
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

        $this->allOnQueue('low');
    }

    public function tags()
    {
        return ['process-trackingmore-postback'];
    }

    public function handle()
    {
        $trackingCode = $this->trackingCode;

        $productPlanSales = ProductPlanSale::with([
            'sale.delivery',
            'tracking'
        ])->whereHas('tracking', function ($query) use ($trackingCode) {
            $query->where('tracking_code', $trackingCode);
        })->get();

        foreach ($productPlanSales as $productPlanSale) {
            $this->trackingService->createOrUpdateTracking($this->trackingCode, $productPlanSale, false, false);
        }
    }
}
