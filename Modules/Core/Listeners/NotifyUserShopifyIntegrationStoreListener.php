<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Events\ShopifyIntegrationReadyEvent;
use Modules\Core\Services\UserNotificationService;
use Modules\Notifications\Notifications\UserShopifyIntegrationStoreNotification;

/**
 * Class NotifyUserShopifyIntegrationStoreListener
 * @package Modules\Core\Listeners
 */
class NotifyUserShopifyIntegrationStoreListener implements ShouldQueue
{
    use Queueable;
    /**
     * @var string
     * @description name of the column in user_notifications table to check if it will send
     */
    private $userNotification = "shopify";

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
            $user = $event->user ?? null;

            /** @var UserNotificationService $userNotificationService */
            $userNotificationService = app(UserNotificationService::class);
            if ($userNotificationService->verifyUserNotification($user, $this->userNotification)) {
                Notification::send($user, new UserShopifyIntegrationStoreNotification($event->user, $event->project));
            }
        } catch (Exception $e) {
            Log::warning('Erro listener shopifyIntegrationReady');
            report($e);
        }
    }
}
