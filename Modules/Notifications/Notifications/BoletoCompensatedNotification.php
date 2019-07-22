<?php

namespace Modules\Notifications\Notifications;

use App\Entities\User;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\PusherService;
use Pusher\Pusher;
use Vinkla\Hashids\Facades\Hashids;

class BoletoCompensatedNotification extends Notification implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;
    /**
     * @var
     */
    private $boletosCount;
    private $user;
    private $pusherService;

    /**
     * Create a new notification instance.
     * @param $user
     * @param $boletoCount
     * @param PusherService $pusherService
     */
    public function __construct($user, $boletoCount, $pusherService)
    {
        $this->user          = $user;
        $this->boletosCount  = $boletoCount;
        $this->pusherService = $pusherService;
    }

    public function broadcastOn()
    {
        return [];
    }

    /**
     * Get the notification's delivery channels.
     * @param mixed $notifiable
     * @return array
     */
    public
    function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'qtd' => $this->boletosCount,
        ];
    }

    /**
     * Get the array representation of the notification.
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        try {
            $dataPusher = [
                'user'    => $this->user,
                'message' => $this->boletosCount . ' boletos compensados',
            ];
            $this->pusherService->sendPusher($dataPusher);
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação com pusher');
            report($e);
        }

        return [
            'user'    => $this->user,
            'message' => $this->boletosCount . ' boletos compensados',
        ];
    }
}
