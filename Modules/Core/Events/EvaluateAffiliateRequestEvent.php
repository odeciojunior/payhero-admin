<?php

namespace Modules\Core\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\AffiliateRequest;

class EvaluateAffiliateRequestEvent
{
    use SerializesModels;
    public $affiliateRequest;

    /**
     * EvaluateAffiliateRequestEvent constructor.
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
