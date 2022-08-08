<?php

namespace Modules\Core\Events;

class CheckSaleHasValidTrackingEvent
{
    public $saleId;

    /**
     * TrackingCodeUpdatedEvent constructor.
     * @param int $saleId
     */
    public function __construct(int $saleId)
    {
        $this->saleId = $saleId;
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
