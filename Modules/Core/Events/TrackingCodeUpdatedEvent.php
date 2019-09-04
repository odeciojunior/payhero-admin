<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\Sale;
use Illuminate\Queue\SerializesModels;

class TrackingCodeUpdatedEvent
{
    use SerializesModels;
    public $sale;

    /**
     * TrackingCodeUpdatedEvent constructor.
     * @param Sale $sale
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
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
