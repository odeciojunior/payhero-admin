<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\PusherService;
use Modules\Core\Events\ShopifyIntegrationReadyEvent;

/**
 * Class NotifyUserShopifyIntegrationReadyListener
 * @package Modules\Core\Listeners
 */
class NotifyUserShopifyIntegrationReadyListener implements ShouldQueue
{
    use Queueable;

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
