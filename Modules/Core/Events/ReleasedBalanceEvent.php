<?php

namespace Modules\Core\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ReleasedBalanceEvent
{
    public $transfer;

    /**
     * ReleasedBalanceEvent constructor.
     * @param Collection $transfer
     */
    public function __construct(Collection $transfer)
    {
        $this->transfer = $transfer;
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
