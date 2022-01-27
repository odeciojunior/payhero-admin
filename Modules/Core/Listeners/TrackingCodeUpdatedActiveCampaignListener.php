<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Tracking;
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
     * @return void
     */
    public function handle(TrackingCodeUpdatedEvent $event)
    {
        try {
            $trackingModel = new Tracking();
            $activeCampaignService = new ActiveCampaignService;

            $tracking = $trackingModel->with([
                'sale.project',
                'sale.customer',
                'sale.productsPlansSale.tracking',
                'sale.productsPlansSale.product',
            ])->find($event->trackingId);

            if ($tracking && $tracking->sale && $tracking->sale->project) {

                $sale = $tracking->sale;
                $customer = $sale->customer;

                $dataCustom = [
                    'url_boleto' => $sale->boleto_link,
                    'sub_total' => $sale->sub_total,
                    'frete' => $sale->shipment_value,
                ];

                $activeCampaignService->execute($sale->id, 6, $customer->name, $customer->telephone, $customer->email, $sale->project_id, 'sale', $dataCustom, $sale->checkout_id); // 6 - tracking
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
