<?php

namespace Modules\Core\Listeners;

use Modules\Core\Events\BilletPaidEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;
use Illuminate\Bus\Queueable;
use Modules\Core\Services\HotsacService;
use Modules\Core\Entities\HotsacIntegration;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\Log;

class BilletPaidHotsacListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param BilletPaidEvent $event
     * @return void
     */
    public function handle(BilletPaidEvent $event)
    {
        try {
            $hotsacIntegration = HotsacIntegration::where('project_id', $event->sale->project_id)
                                                  ->where('billet_paid', 1)
                                                  ->first();

            if (!empty($hotsacIntegration)) {
                $hotsacService = new HotsacService($hotsacIntegration->url_api, $hotsacIntegration->id);
                $sale = $event->sale;
                $sale->setRelation('customer', $event->customer);
                $sale->load('plansSales.plan', 'delivery');
                $domain = Domain::where('status', 3)->where('project_id', $sale->project_id)->first();
                return $hotsacService->sendSale($sale, $sale->plansSales, $domain, 'billet_paid');
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação para HotSac na venda ' . $event->sale->id);
            report($e);
        }
    }
}
