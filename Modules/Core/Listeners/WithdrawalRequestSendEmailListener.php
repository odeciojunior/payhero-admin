<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\SendgridService;

/**
 * Class WithdrawalRequestSendEmailListener
 * @package Modules\Core\Listeners
 */
class WithdrawalRequestSendEmailListener implements ShouldQueue
{
    use Queueable;

    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param WithdrawalRequestEvent $event
     */
    public function handle(WithdrawalRequestEvent $event)
    {
        $sendGridService = new SendgridService();
        $userName = $event->withdrawal->company->user->name;
        $data = [
            "name" => $userName,
            "value" => number_format(intval($event->withdrawal->value) / 100, 2, ",", "."),
        ];
        $sendGridService->sendEmail(
            "noreply@nexuspay.com.br",
            "NexusPay",
            $event->withdrawal->company->user->email,
            $userName,
            "d-2548bf18de634ee1ab6bc23cf4d7ba25", // done
            $data
        );
    }
}
