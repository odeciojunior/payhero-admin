<?php

namespace Modules\Core\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Affiliate;

class AffiliateEvent
{
    use SerializesModels;
    public $affiliate;

    /**
     * AffiliateEvent constructor.
     * @param Affiliate $affiliate
     */
    public function __construct(Affiliate $affiliate)
    {
        $this->affiliate = $affiliate;
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
