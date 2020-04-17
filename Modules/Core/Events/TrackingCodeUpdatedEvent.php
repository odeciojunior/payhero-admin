<?php

namespace Modules\Core\Events;

use Illuminate\Support\Collection;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;

class TrackingCodeUpdatedEvent
{
    public $sale;
    public $tracking;
    public $products;

    /**
     * TrackingCodeUpdatedEvent constructor.
     * @param Sale $sale
     * @param Tracking $tracking
     * @param Collection $products
     */
    public function __construct(Sale $sale, Tracking $tracking, Collection $products)
    {
        $this->sale = $sale;
        $this->tracking = $tracking;
        $this->products = $products;
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
