<?php

namespace Modules\Notifications\Notifications;

use App\Entities\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use Vinkla\Hashids\Facades\Hashids;

class boletoCompensatedNotification extends Notification
{
    use Queueable;
    /**
     * @var
     */
    private $boletosCount;

    /**
     * Create a new notification instance.
     * @param User $user
     * @param $boletoCount
     */
    public function __construct($boletoCount)
    {

        $this->boletosCount = $boletoCount;
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
            $options    = [
                'cluster' => 'us2',
                'useTLS'  => true,
            ];
            $pusher     = new Pusher(
                '339254dee7e0c0a31840',
                '78ed93b50a3327693d20',
                '724843',
                $options
            );
            $dataPusher = [
                'message' => 6 . ' boletos compensados',
            ];
            $pusher->trigger('channel-' . Hashids::connection('pusher_connection')
                                                 ->encode(auth()->user()->id), 'new-notification', $dataPusher);
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação com pusher');
            report($e);
        }

        return [
            '',
        ];
    }
}
