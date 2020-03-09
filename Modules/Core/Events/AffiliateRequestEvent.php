<?php

namespace Modules\Core\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\AffiliateRequest;

class AffiliateRequestEvent
{
    use SerializesModels;
    public $affiliateRequest;

    /**
     * AffiliateRequestEvent constructor.
     * @param AffiliateRequest $affiliateRequest
     */
    public function __construct(AffiliateRequest $affiliateRequest)
    {
        $this->affiliateRequest = $affiliateRequest;
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
