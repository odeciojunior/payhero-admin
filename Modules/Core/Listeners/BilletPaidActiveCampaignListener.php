<?php

namespace Modules\Core\Listeners;

use Modules\Core\Events\BilletPaidEvent;
use Modules\Core\Services\ActiveCampaignService;


class BilletPaidActiveCampaignListener
{
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
     * @return void
     */
    public function handle(BilletPaidEvent $event)
    {
        try {
            $activeCampaignService = new ActiveCampaignService;
            $sale = $event->sale;
            $client = $event->client;
            // execute($saleId, $eventSale, $name, $phone, $email, $projectId)
            return $activeCampaignService->execute($sale->id, 2, $client->name, $client->telephone, $client->email, $sale->project_id, 'sale'); // 2 - boleto pago
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
            report($e);
        }
    }
}
