<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Modules\Core\Entities\Checkout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCheckoutTableListener implements ShouldQueue
{
    use Queueable;

    public function __construct(){

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $checkoutModel = new Checkout();

        $abandonedCarts = $checkoutModel->select('checkouts.id', 'checkouts.created_at', 'checkouts.project_id', 'checkouts.id_log_session', 'checkouts.status', 'checkouts.email_sent_amount', 'checkouts.sms_sent_amount', 'logs.name', 'logs.telephone')
                                        ->leftjoin('logs', function($join) {
                                            $join->on('logs.id', '=', DB::raw("(select max(logs.id) from logs WHERE logs.id_log_session = checkouts.id_log_session)"));
                                        })
                                        ->whereIn('status', ['recovered', 'abandoned cart'])
                                        ->whereNull('client_name')
                                        ->get();

        foreach ($abandonedCarts as $abandonedCart) {
            $abandonedCart->update([
                                    'client_name'      => $abandonedCart->name,
                                    'client_telephone' => $abandonedCart->telephone,
                                ]);
        }
    }
}
