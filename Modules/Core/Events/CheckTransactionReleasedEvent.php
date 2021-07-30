<?php

namespace Modules\Core\Events;

class CheckTransactionReleasedEvent
{
    public $transactionId;

    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    public function broadcastOn(): array
    {
        return [];
    }
}
