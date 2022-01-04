<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\NewChargebackEvent;

class NotifyAntifraudChargebackListener implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(NewChargebackEvent $event)
    {
        try {
            $sale = $event->sale;
//            TODO: Change to new antifraud
//            (new NethoneAntifraudService())->updateTransactionStatus($sale->id, $sale->present()->getStatus($sale->status));
        } catch (Exception $e) {
            report($e);
        }
    }
}
