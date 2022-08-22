<?php

namespace Modules\Core\Events;

use Illuminate\Broadcasting\Channel;
use Modules\Core\Entities\Sale;

/**
 * Class PixExpiredEvent
 * @package Modules\Core\Events
 */
class PixExpiredEvent
{
    /**
     * @var Sale
     */
    public Sale $sale;

    /**
     * Create a new event instance.
     * @param Sale $sale
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /**
     * Get the channels the event should broadcast on.
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}
