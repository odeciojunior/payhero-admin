<?php

namespace App\Listeners\Modules\Core\Listeners;

use Exception;
use Modules\Core\Entities\User;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Notification;
use App\Events\Modules\Core\Events\SaleApprovedEvent;
use Modules\Core\Services\UserNotificationService;
use Modules\Notifications\Notifications\SaleApprovedNotification;

class NotifyUsersApprovedSaleListener
{
    /**
     * @var string
     * @description name of the column in user_notifications table to check if it will send
     */
    private $userNotification = "sale_approved";

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
     * @param SaleApprovedEvent $event
     * @return void
     */
    public function handle(SaleApprovedEvent $event)
    {
        try {
            $userModel = new User();
            $user      = $userModel->find($event->project->owner);

            $notification = Notification::where([
                                                    ['notifiable_id', $user->id],
                                                    ['type', 'Modules\Checkout\Notifications\SaleNotification'],
                                                ])
                                        ->whereNull('read_at')
                                        ->first();

            if ($notification) {
                $data = json_decode($notification['data']);
                $notification->update([
                                          'data' => json_encode(['qtd' => preg_replace("/[^0-9]/", "", $data->qtd) + 1]),
                                      ]);
            } else {
                /** @var UserNotificationService $userNotificationService */
                $userNotificationService = app(UserNotificationService::class);
                if ($userNotificationService->verifyUserNotification($user, $this->userNotification)) {
                    $user->notify(new SaleApprovedNotification());
                }
            }
        } catch (Exception $e) {
            Log::warning('erro ao criar nova notificação');
            report($e);
        }
    }
}
