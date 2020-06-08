<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\UnicodropIntegration;
use Modules\Core\Events\BilletPaidEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;
use Illuminate\Bus\Queueable;
use Modules\Core\Services\ReportanaService;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\UnicodropService;

class BilletPaidUnicodropListener
{
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * @param BilletPaidEvent $event
     * @return void
     */
    public function handle(BilletPaidEvent $event)
    {
        try {
            $unicodropIntegration = UnicodropIntegration::where('project_id', $event->sale->project_id)
                                                        ->where('billet_paid', 1)
                                                        ->first();

            if (!empty($unicodropIntegration)) {
                $unicodropService = new UnicodropService($unicodropIntegration->token, $unicodropIntegration->id);
                $sale             = $event->sale;
                $sale->setRelation('customer', $event->customer);
                $sale->load('plansSales.plan', 'delivery');
                $unicodropService->boletoPaid($event->sale);
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação para Unicodrop na venda ' . $event->sale->id);
            report($e);
        }
    }
}
