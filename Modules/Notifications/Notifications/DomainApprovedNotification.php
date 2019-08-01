<?php

namespace Modules\Notifications\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Vinkla\Hashids\Facades\Hashids;

class DomainApprovedNotification extends Notification
{
    /**
     * @var string
     */
    private $message;
    /**
     * @var int
     */
    private $project;

    /**
     * Create a new notification instance.
     * @param string $message
     * @param int $project
     */
    public function __construct(string $message, int $project)
    {
        $this->message = $message;
        $this->project = $project;
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
            'project' => Hashids::encode($this->project),
        ];
    }
}
