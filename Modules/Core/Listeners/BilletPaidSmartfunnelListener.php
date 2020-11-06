<?php

namespace Modules\Core\Listeners;

use Modules\Core\Events\BilletPaidEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;
use Illuminate\Bus\Queueable;
use Modules\Core\Services\SmartfunnelService;
use Modules\Core\Entities\SmartfunnelIntegration;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\Log;

class BilletPaidSmartfunnelListener
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
            $smartfunnel = SmartfunnelIntegration::where('project_id', $event->sale->project_id)->first();

            if (!empty($smartfunnel)) {
                $smartfunnelService = new SmartfunnelService($smartfunnel->api_url, $smartfunnel->id);
                $sale = $event->sale;
                $sale->setRelation('customer', $event->customer);
                $sale->load('plansSales.plan', 'delivery');
                $domain = Domain::where('status', 3)->where('project_id', $sale->project_id)->first();
                return $smartfunnelService->sendSale($sale, $sale->plansSales, $domain, 'billet_paid');
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação para Smart Funnel na venda ' . $event->sale->id);
            report($e);
        }
    }
}
