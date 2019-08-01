<?php

namespace Modules\Core\Listeners;

use Modules\Core\Events\ShopifyIntegrationReadyEvent;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Services\PusherService;
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

        try {
            $pusherService = new PusherService();

            $data = [
                'message' => 'Integração do seu projeto ' . $event->project->name . 'com o shopify está pronto',
                'user'    => $event->user->id,
            ];

            $pusherService->sendPusher($data);
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação com pusher');
            report($e);
        }
    }
}
