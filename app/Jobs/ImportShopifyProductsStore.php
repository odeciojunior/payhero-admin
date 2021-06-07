<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Services\ShopifyService;

class ImportShopifyProductsStore implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $shopifyIntegrantion;
    public $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shopifyIntegration, $userId)
    {
        $this->shopifyIntegrantion = $shopifyIntegration;
        $this->userId = $userId;
    }

    public function handle()
    {
        $shopifyService = new ShopifyService($this->shopifyIntegrantion->url_store, $this->shopifyIntegrantion->token);
        $shopifyService->importShopifyStore($this->shopifyIntegrantion->project->id, $this->userId);
    }
}
