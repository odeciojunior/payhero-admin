<?php

namespace Modules\Core\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Withdrawal;

class WithdrawalRequestEvent
{
    use SerializesModels;
    public $withdrawal;

    /**
     * WithdrawalApprovedEvent constructor.
     * @param Withdrawal $withdrawal
     */
    public function __construct(Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
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
