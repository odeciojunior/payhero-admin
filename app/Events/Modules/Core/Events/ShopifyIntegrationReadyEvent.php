<?php

namespace App\Events\Modules\Core\Events;

use App\Entities\Project;
use App\Entities\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ShopifyIntegrationReadyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var User
     */
    public $user;
    /**
     * @var Project
     */
    public $project;

    /**
     * Create a new event instance.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
