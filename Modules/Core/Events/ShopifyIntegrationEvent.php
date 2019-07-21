<?php

namespace Modules\Core\Events;

use Illuminate\Queue\SerializesModels;

class ShopifyIntegrationEvent
{
    use SerializesModels;

    protected $projectId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}
