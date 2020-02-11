<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Modules\Core\Entities\Checkout;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCheckoutTableListener implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {

    }

    /**
     * Handle the event.
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $checkoutModel = new Checkout();

        $abandonedCarts = $checkoutModel->select('checkouts.id', 'checkouts.created_at', 'checkouts.project_id', 'checkouts.status', 'checkouts.email_sent_amount', 'checkouts.sms_sent_amount', 'logs.name', 'logs.telephone')
                                        ->leftjoin('logs', function($join) {
                                            $join->on('logs.id', '=', DB::raw("(select max(logs.id) from logs WHERE logs.checkout_id = checkouts.id)"));
                                        })
                                        ->whereIn('status_enum', [
                                            $checkoutModel->present()->getStatusEnum('recovered'),
                                            $checkoutModel->present()->getStatusEnum('abandoned cart')
                                        ])
                                        ->whereNull('client_name')
                                        ->take(1000)->get();

        foreach ($abandonedCarts as $abandonedCart) {
            $abandonedCart->update([
                                       'client_name'      => $abandonedCart->name,
                                       'client_telephone' => $abandonedCart->telephone,
                                   ]);
        }
    }
}
