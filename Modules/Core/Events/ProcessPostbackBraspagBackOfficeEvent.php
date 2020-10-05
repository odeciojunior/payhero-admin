<?php

namespace Modules\Core\Events;

class ProcessPostbackBraspagBackOfficeEvent
{
    public string $merchantId;
    public string $status;

    public function __construct(string $merchantId, string $status)
    {
        $this->merchantId = $merchantId;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return [];
    }
}
