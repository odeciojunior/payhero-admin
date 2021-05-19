<?php

namespace Modules\Core\Events;

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
