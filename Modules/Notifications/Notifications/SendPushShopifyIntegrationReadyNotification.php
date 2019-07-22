<?php

namespace Modules\Notifications\Notifications;

use App\Entities\Project;
use App\Entities\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\PusherService;

class SendPushShopifyIntegrationReadyNotification extends Notification
{
    use Queueable;
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
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Integração do seu projeto ' . $this->project . 'com o shopify está pronto',
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
            $pusherService = new PusherService();

            $data = [
                'message' => 'Integração do seu projeto ' . $this->project . 'com o shopify está pronto',
            ];

            $pusherService->sendPusher($data);
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação com pusher');
            report($e);
        }

        return [
            'message' => 'Integração do seu projeto ' . $this->project . 'com o shopify está pronto',
        ];
    }
}
