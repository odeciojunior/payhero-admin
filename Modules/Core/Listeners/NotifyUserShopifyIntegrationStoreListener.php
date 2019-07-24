<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Events\ShopifyIntegrationReadyEvent;
use Modules\Notifications\Notifications\SendPushShopifyIntegrationReadyNotification;
use Modules\Notifications\Notifications\UserShopifyIntegrationStoreNotification;

class NotifyUserShopifyIntegrationStoreListener
{
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * @param object $event
     * @return void
     */
    public function handle(ShopifyIntegrationReadyEvent $event)
    {
        try {
            Notification::send($event->user, new UserShopifyIntegrationStoreNotification($event->user, $event->project));
        } catch (Exception $e) {
            Log::warning('Erro listener shopifyIntegrationReady');
            report($e);
        }
    }
}
