<?php

namespace Modules\Notifications\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Vinkla\Hashids\Facades\Hashids;

class ReleasedBalanceNotification extends Notification
{
    /**
     * @var string
     */
    private $message;

    /**
     * ReleasedBalanceNotification constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}
