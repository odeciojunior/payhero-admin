<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\JsonResponse;
use Modules\Core\Events\BilletPaidEvent;
use Modules\Core\Services\ActiveCampaignService;

/**
 * Class BilletPaidActiveCampaignListener
 * @package Modules\Core\Listeners
 */
class BilletPaidActiveCampaignListener implements ShouldQueue
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
     * @param BilletPaidEvent $event
     * @return JsonResponse
     */
    public function handle(BilletPaidEvent $event)
    {
        try {
            $activeCampaignService = new ActiveCampaignService;
            $sale = $event->sale;
            $customer = $event->customer;

            $dataCustom = [
                'url_boleto' => $sale->boleto_link,
                'sub_total' => $sale->sub_total,
                'frete' => $sale->shipment_value
            ];

            // execute($saleId, $eventSale, $name, $phone, $email, $projectId)
            return $activeCampaignService->execute($sale->id, 2, $customer->name, $customer->telephone, $customer->email, $sale->project_id, 'sale', $dataCustom, $sale->checkout_id); // 2 - boleto pago
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }
}
