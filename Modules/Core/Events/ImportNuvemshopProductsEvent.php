<?php

namespace Modules\Core\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\NuvemshopIntegration;

class ImportNuvemshopProductsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $integration;

    public function __construct(NuvemshopIntegration $integration)
    {
        $this->integration = $integration;
    }
}
