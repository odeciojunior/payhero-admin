<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\ShopifyService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportShopifyStoreListener implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        try {
            $shopifyService = new ShopifyService($event->shopifyIntegration->url_store, $event->shopifyIntegration->token);

            $shopifyService->importShopifyStore($event->shopifyIntegration->project, $event->userId);
        } catch (Exception $e) {
            Log::warning('Erro ao importar loja do shopify');
            report($e);
        }
    }
}
