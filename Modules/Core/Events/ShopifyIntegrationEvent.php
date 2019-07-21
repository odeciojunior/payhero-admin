<?php

namespace Modules\Core\Events;

use Illuminate\Queue\SerializesModels;

class ShopifyIntegrationEvent
{
    use SerializesModels;

    public $projectId;
    public $shopifyService;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($projectId, $shopifyService)
    {
        $this->projectId      = $projectId;
        $this->shopifyService = $shopifyService;
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
