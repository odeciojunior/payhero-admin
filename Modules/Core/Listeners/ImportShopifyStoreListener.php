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
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 7200;

    /**
     * Handle the event.
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        try {
            /** @var ShopifyService $shopifyService */
            $shopifyService = new ShopifyService($event->shopifyIntegration->url_store, $event->shopifyIntegration->token);
            $shopifyService->importShopifyStore($event->shopifyIntegration->project->id, $event->userId);
        } catch (Exception $e) {
            Log::warning('Erro ao importar loja do shopify');
            report($e);
        }
    }


    /**
     * Handle a job failure.
     *
     * @param  \App\Events\OrderShipped  $event
     * @param  \Exception  $exception
     * @return void
     */
    public function failed($event, $exception)
    {
        report($exception);
    }
}
