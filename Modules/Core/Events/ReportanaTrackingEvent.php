<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\Sale;

class ReportanaTrackingEvent
{
    public Sale $sale;
    public bool $trackingCreatedEvent;

    public function __construct(int $saleId, bool $trackingCreatedEvent = true)
    {
        $this->sale = Sale::find($saleId);
        $this->trackingCreatedEvent = $trackingCreatedEvent;
    }
}
