<?php

namespace Modules\Notifications\Notifications;

use Modules\Core\Entities\User;
use Illuminate\Notifications\Notification;

class SalesExportedNotification extends Notification
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $filename;

    /**
     * Create a new notification instance.
     * @param User $user
     * @param string $filename
     */
    public function __construct(User $user, string $filename)
    {
        $this->user = $user;
        $this->filename = $filename;
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
            'message' => $this->filename,
        ];
    }
}
