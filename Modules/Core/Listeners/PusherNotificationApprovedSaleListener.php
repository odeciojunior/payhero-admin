<?php

namespace App\Listeners\Modules\Core\Listeners;

use App\Entities\User;
use App\Events\Modules\Core\Events\SaleApprovedEvent;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\PusherService;

class PusherNotificationApprovedSaleListener
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

            $pusher = new PusherService();

            $user = $userModel->find($event->sale->owner);

            if ($event->sale->payment_method == 1 && !empty($user)) {
                $message = 'Venda aprovada no projeto ' . $event->project->name;

                $data = [
                    'user'    => $user->id,
                    'message' => $message,
                ];

                $pusher->sendPusher($data);
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação com pusher');
            report($e);
        }
    }
}
