<?php

namespace Modules\Core\Events;

class CheckTransactionReleasedEvent
{
    public int $transactionId;

    public function __construct(int $transactionId)
    {
        $this->transactionId = $transactionId;
    }

    public function broadcastOn(): array
    {
        return [];
    }
}
