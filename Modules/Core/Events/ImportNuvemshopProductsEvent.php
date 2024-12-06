<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\NuvemshopIntegration;

class ImportNuvemshopProductsEvent
{
    public $integration;

    public function __construct(NuvemshopIntegration $integration)
    {
        $this->integration = $integration;
    }
}
