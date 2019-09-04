<?php

namespace Modules\Core\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\ShopifyIntegration;

class ShopifyIntegrationEvent
{
    use SerializesModels;

    public $shopifyIntegration;
    public $userId;

    /**
     * Create a new event instance.
     * @param ShopifyIntegration $shopifyIntegration
     * @param $userId
     */
    public function __construct(ShopifyIntegration $shopifyIntegration, $userId)
    {
        $this->shopifyIntegration = $shopifyIntegration;
        $this->userId             = $userId;
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
