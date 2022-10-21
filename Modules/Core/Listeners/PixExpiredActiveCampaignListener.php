<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\ActiveCampaignService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class TrackingCodeUpdatedActiveCampaignListener
 * @package Modules\Core\Listeners
 */
class PixExpiredActiveCampaignListener implements ShouldQueue
{
    use Queueable;

    /**
     * @param TrackingCodeUpdatedEvent $event
     * @return void
     */
    public function handle(TrackingCodeUpdatedEvent $event)
    {
        try {
            $activeCampaignService = new ActiveCampaignService();

            $sale = $event->sale;

            $customer = $sale->customer;

            $dataCustom = [
                //"url_boleto" => $sale->boleto_link,
                "sub_total" => $sale->sub_total,
                "frete" => $sale->shipment_value,
                "codigo_pedido" => Hashids::encode($sale->checkout_id),
                "projeto_nome" => $sale->project->name,
            ];

            $activeCampaignService->execute(
                $sale->id,
                9,
                $customer->name,
                $customer->telephone,
                $customer->email,
                $sale->project_id,
                "sale",
                $dataCustom,
                $sale->checkout_id
            ); // 9 Pix expired
        } catch (Exception $e) {
            report($e);
        }
    }
}