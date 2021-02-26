<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Services\ShopifyService;

class ImportShopifyProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $project;
    private $productId;
    private $userId;

    public function __construct($project, $userId, $productId)
    {
        $this->project = $project;
        $this->userId = $userId;
        $this->productId = $productId;
    }

    public function handle()
    {
        $integration = $this->project->shopifyIntegrations->first();

        $shopifyService = new ShopifyService($integration->url_store, $integration->token, false);

        $shopifyService->importShopifyProduct($this->project->id, $this->userId, $this->productId);
    }
}
