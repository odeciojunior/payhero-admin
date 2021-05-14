<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\ShopifyService;

/**
 * Class ImportShopifyStoreListener
 * @package Modules\Core\Listeners
 */
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
            $shopifyService->importShopifyStore($event->shopifyIntegration->project->id, $event->userId);
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @param $event
     * @param $exception
     */
    public function failed($event, $exception)
    {
        report($exception);
    }
}
