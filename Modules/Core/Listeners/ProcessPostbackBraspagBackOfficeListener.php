<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\ProcessPostbackBraspagBackOfficeEvent;
use Modules\Core\Services\CompanyServiceBraspag;

class ProcessPostbackBraspagBackOfficeListener implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(ProcessPostbackBraspagBackOfficeEvent $event)
    {
        CompanyServiceBraspag::processPostback($event->merchantId, $event->status);
    }
}
