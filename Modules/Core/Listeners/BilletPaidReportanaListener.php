<?php

namespace Modules\Core\Listeners;

use Modules\Core\Events\BilletPaidEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;
use Illuminate\Bus\Queueable;
use Modules\Core\Services\ReportanaService;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\Log;

class BilletPaidReportanaListener
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
            $reportanaIntegration = ReportanaIntegration::where('project_id', $event->sale->project_id)
                                                        ->where('billet_paid', 1)
                                                        ->first();

            if (!empty($reportanaIntegration)) {
                $reportanaService = new ReportanaService($reportanaIntegration->url_api, $reportanaIntegration->id);
                $sale             = $event->sale;
                $sale->setRelation('customer', $event->customer);
                $sale->load('plansSales.plan', 'delivery');
                $domain = Domain::where('status', 3)->where('project_id', $sale->project_id)->first();
                return $reportanaService->sendSale($sale, $sale->plansSales, $domain, 'billet_paid');
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação para Reportana na venda ' . $event->sale->id);
            report($e);
        }
    }
}
