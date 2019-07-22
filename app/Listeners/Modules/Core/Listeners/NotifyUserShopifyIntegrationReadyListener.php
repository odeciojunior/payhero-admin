<?php

namespace App\Listeners\Modules\Core\Listeners;

use App\Events\Modules\Core\Events\ShopifyIntegrationReadyEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Notifications\Notifications\SendPushShopifyIntegrationReadyNotification;

class NotifyUserShopifyIntegrationReadyListener
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
     * @param ShopifyIntegrationReadyEvent $event
     * @return void
     */
    public function handle(ShopifyIntegrationReadyEvent $event)
    {
        Notification::send($event->user, new SendPushShopifyIntegrationReadyNotification($event->user, $event->project));
    }
}
