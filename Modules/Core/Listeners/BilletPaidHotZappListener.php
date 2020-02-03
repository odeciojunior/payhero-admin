<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\BilletPaidEvent;
use Modules\Core\Services\HotZappService;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\ConvertaxIntegration;
use Illuminate\Support\Facades\Log;

/**
 * Class BilletPaidHotZappListener
 * @package Modules\Core\Listeners
 */
class BilletPaidHotZappListener implements ShouldQueue
{
    use Queueable;

    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * @param BilletPaidEvent $event
     */
    public function handle(BilletPaidEvent $event)
    {
        try {
            $hotZappIntegrationModel = new HotZappIntegration();
            $hotzappIntegration      = $hotZappIntegrationModel->where('project_id', $event->plan->project_id)
                                                               ->where('boleto_paid', 1)
                                                               ->first();

            if (!empty($hotzappIntegration)) {
                $hotZappService = new HotZappService($hotzappIntegration->link);
                $hotZappService->boletoPaid($event->sale);
            }

            $convertaxIntegrationModel = new ConvertaxIntegration();
            $convertaxIntegration      = $convertaxIntegrationModel->where('project_id', $event->plan->project_id)
                                                                   ->where('boleto_paid', 1)
                                                                   ->first();

            if (!empty($convertaxIntegration)) {
                $hotZappService = new HotZappService($convertaxIntegration->link);
                $hotZappService->boletoPaid($event->sale);
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação pro HotZapp na venda ' . $event->sale->id);
            report($e);
        }
    }
}
