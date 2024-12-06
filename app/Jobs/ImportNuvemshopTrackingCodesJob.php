<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\NuvemshopIntegration;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\Nuvemshop\NuvemshopAPI;
use Modules\Core\Services\Nuvemshop\NuvemshopService;

class ImportNuvemshopTrackingCodesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private NuvemshopIntegration $integration;
    private NuvemshopAPI $api;
    private NuvemshopService $service;

    public function __construct(NuvemshopIntegration $integration)
    {
        $this->allOnQueue("low");

        $this->integration = $integration;
        $this->api = new NuvemshopAPI($this->integration->store_id, $this->integration->token);
        $this->service = new NuvemshopService($this->integration);
    }

    public function handle()
    {
        $this->service->createWebhooks();

        Sale::where("project_id", $this->integration->project_id)
            ->where("status", Sale::STATUS_APPROVED)
            ->whereNotNull("nuvemshop_order")
            ->whereNotNull("delivery_id")
            ->whereHas("productsPlansSale", function ($query) {
                $query->whereDoesntHave("tracking");
            })
            ->whereHas("transactions", function ($query) {
                $query->where("tracking_required", true);
            })
            ->chunk(1000, function ($sales) {
                foreach ($sales as $sale) {
                    try {
                        $order = $this->api->findOrder($sale->nuvemshop_order);
                        $this->service->fulfillOrder($order);
                    } catch (\Exception $e) {
                        report($e);
                    }
                }
            });
    }
}
