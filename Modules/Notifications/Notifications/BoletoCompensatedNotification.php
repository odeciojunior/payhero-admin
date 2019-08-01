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

class BoletoCompensatedNotification extends Notification
{
    /**
     * @var
     */
    private $boletoCount;

    /**
     * Create a new notification instance.
     * @param $user
     * @param $boletoCount
     * @param PusherService $pusherService
     */
    public function __construct($boletoCount)
    {
        $this->boletoCount = $boletoCount;
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
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'qtd' => $this->boletoCount,
        ];
    }
}
