<?php

namespace Modules\Notifications\Notifications;

use App\Entities\Project;
use App\Entities\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use Vinkla\Hashids\Facades\Hashids;

class UserShopifyIntegrationStoreNotification extends Notification
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var Project
     */
    private $project;

    /**
     * Create a new notification instance.
     * @param User $user
     * @param Project $project
     */
    public function __construct(User $user, Project $project)
    {
        $this->user    = $user;
        $this->project = $project;
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
            'message' => 'Integração do seu projeto ' . $this->project->name . ' com o shopify está concluida',
            'link'    => Hashids::encode($this->project->id),
        ];
    }
}
