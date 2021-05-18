<?php

namespace Modules\Notifications\Notifications;

use App\Entities\Project;
use App\Entities\User;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\PusherService;
use Vinkla\Hashids\Facades\Hashids;

class SendPushShopifyIntegrationReadyNotification extends Notification
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

    public function via($notifiable)
    {
        return [];
    }

    /**
     * Get the array representation of the notification.
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        try {
            $pusherService = new PusherService();

            $data = [
                'message' => 'Integração do seu projeto ' . $this->project->name . 'com o shopify está pronto',
                'user'    => $this->user->id,
            ];

            $pusherService->sendPusher($data);
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação com pusher');
            report($e);
        }
    }
}
