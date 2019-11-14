<?php

namespace Modules\Notifications\Notifications;

use Modules\Core\Entities\User;
use Illuminate\Notifications\Notification;

class TrackingsImportedNotification extends Notification
{
    /**
     * @var User
     */
    private $user;

    /**
     * Create a new notification instance.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user    = $user;
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
            'message' => 'Importação dos códigos de rastreio concluída.',
        ];
    }
}
