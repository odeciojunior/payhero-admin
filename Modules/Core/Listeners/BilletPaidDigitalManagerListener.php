<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\DigitalManagerService;
use Modules\Core\Entities\DigitalmanagerIntegration;
use Modules\Core\Entities\ProductPlanSale;
use Illuminate\Support\Facades\Log;

/**
 * Class BilletPaidDigitalManagerListener
 * @package Modules\Core\Listeners
 */
class BilletPaidDigitalManagerListener implements ShouldQueue
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
     * Handle the event.
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        try {
            $digitalManagerIntegration = DigitalmanagerIntegration::where('project_id', $event->sale->project_id)
                                                                  ->where('billet_paid', 1)
                                                                  ->first();

            if (!empty($digitalManagerIntegration)) {
                $digitalManagerService = new DigitalManagerService($digitalManagerIntegration->url, $digitalManagerIntegration->api_token, $digitalManagerIntegration->id);
                $sale                  = $event->sale;
                $sale->load('client', 'delivery', 'checkout');
                $productPlanSale = ProductPlanSale::where('sale_id', $sale->id)->first();
                $productPlanSale->load('product');

                return $digitalManagerService->sendSale($event->sale, $productPlanSale, $productPlanSale->product, 2);
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação para Digital Manager na venda ' . $event->sale->id);
            report($e);
        }
    }
}
