<?php

namespace Modules\Core\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Modules\Core\Services\ShopifyService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportShopifyStoreListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $shopifyService = new ShopifyService($event->shopifyIntegration->url_store, $event->shopifyIntegration->token);

        $shopifyService->importShopifyStore($event->shopifyIntegration->project, $event->userId);
    }

}
