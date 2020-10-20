<?php

namespace Modules\Core\Events;

use Illuminate\Support\Collection;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;

class TrackingCodeUpdatedEvent
{
    public $trackingId;

    /**
     * TrackingCodeUpdatedEvent constructor.
     * @param int $trackingId
     */
    public function __construct(int $trackingId)
    {
        $this->trackingId = $trackingId;
    }

    /**
     * Get the channels the event should be broadcast on.
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
