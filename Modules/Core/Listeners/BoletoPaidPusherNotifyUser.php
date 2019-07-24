<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Events\BoletoPaidEvent;
use Modules\Core\Services\PusherService;

class BoletoPaidPusherNotifyUser
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
     * @param BoletoPaidEvent $event
     * @return void
     */
    public function handle(BoletoPaidEvent $event)
    {
        try {

            $pusherService = new PusherService();

            $data = $event->data;

            $dataPusher = [
                'user'    => $data['user']->id,
                'message' => $data['boleto_count'] . ' ' . $data['message'],
            ];

            $pusherService->sendPusher($dataPusher);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar enviar notificação de boleto compensado');
            report($e);
        }
    }
}
