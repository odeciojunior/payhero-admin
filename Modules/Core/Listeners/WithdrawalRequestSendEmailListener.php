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
        $userName        = $event->withdrawal->company->user->name;
        $data            = [
            'name'  => $userName,
            'date'  => $event->withdrawal->created_at->format('d/m/Y'),
            'value' => number_format(intval($event->withdrawal->value) / 100, 2, ',', '.'),
        ];
        $sendGridService->sendEmail('noreply@cloudfox.net', 'cloudfox', 'financeiro@cloudfox.net', 'Financeiro', 'd-3c2e86c9fefb412fad28ffcdf2d87768', $data);
    }
}
