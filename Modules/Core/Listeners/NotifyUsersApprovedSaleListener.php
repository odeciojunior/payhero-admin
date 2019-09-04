<?php

namespace App\Listeners\Modules\Core\Listeners;

use Modules\Core\Entities\User;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Notification;
use App\Events\Modules\Core\Events\SaleApprovedEvent;
use Modules\Notifications\Notifications\SaleApprovedNotification;

class NotifyUsersApprovedSaleListener
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
                $user->notify(new SaleApprovedNotification());
            }
        } catch (\Exception $e) {
            Log::warning('erro ao criar nova notificação');
            report($e);
        }
    }
}
