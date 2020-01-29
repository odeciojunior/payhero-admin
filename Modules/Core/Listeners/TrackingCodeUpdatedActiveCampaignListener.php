<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Http\JsonResponse;
use Modules\Core\Services\ActiveCampaignService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\TrackingCodeUpdatedEvent;

/**
 * Class TrackingCodeUpdatedActiveCampaignListener
 * @package Modules\Core\Listeners
 */
class TrackingCodeUpdatedActiveCampaignListener implements ShouldQueue
{
    use Queueable;

    /**
     * @param TrackingCodeUpdatedEvent $event
     * @return JsonResponse
     */
    public function handle(TrackingCodeUpdatedEvent $event)
    {
        try {
            $activeCampaignService = new ActiveCampaignService;
            $sale = $event->sale;
            $customer = $event->sale->customer;

            $dataCustom = [
                'url_boleto' => $sale->boleto_link,
                'sub_total' => $sale->sub_total,
                'frete' => $sale->shipment_value
            ];

            return $activeCampaignService->execute($sale->id, 6, $customer->name, $customer->telephone, $customer->email, $sale->project_id, 'sale', $dataCustom); // 6 - tracking
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }
}
