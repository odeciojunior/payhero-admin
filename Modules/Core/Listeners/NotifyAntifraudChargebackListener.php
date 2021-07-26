<?php

namespace Modules\Core\Listeners;

use Exception;
use Modules\Core\Events\NewChargebackEvent;
use Modules\Core\Services\Antifraud\NethoneAntifraudService;

class NotifyAntifraudChargebackListener
{
    public function __construct()
    {
        //
    }

    public function handle(NewChargebackEvent $event)
    {
        try {
            $sale = $event->sale;
            (new NethoneAntifraudService())->updateTransactionStatus($sale->id, $sale->present()->getStatus($sale->status));
        } catch (Exception $e) {
            report($e);
        }
    }
}
