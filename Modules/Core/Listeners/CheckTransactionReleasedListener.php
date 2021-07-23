<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\CheckTransactionReleasedEvent;
use Modules\Core\Services\CheckoutService;

class CheckTransactionReleasedListener implements ShouldQueue
{
    use Queueable;

    /**
     * @throws Exception
     */
    public function handle(CheckTransactionReleasedEvent $event)
    {
        (new CheckoutService())->releasePaymentGetnet($event->transactionId);
    }
}
