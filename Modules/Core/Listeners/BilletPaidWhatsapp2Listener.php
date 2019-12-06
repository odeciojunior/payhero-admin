<?php

namespace Modules\Core\Listeners;

use Modules\Core\Events\BilletPaidEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;
use Illuminate\Bus\Queueable;
use Modules\Core\Services\Whatsapp2Service;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\Log;

class BilletPaidWhatsapp2Listener
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
     * @param  BilletPaidEvent  $event
     * @return void
     */
    public function handle(BilletPaidEvent $event)
    {
        try {
            $whatsapp2Integration = Whatsapp2Integration::where('project_id', $event->sale->project_id)
                                                        ->where('billet_paid', 1)
                                                        ->first();

            if (!empty($whatsapp2Integration)) {
                $whatsapp2Service = new Whatsapp2Service($whatsapp2Integration->url_checkout, $whatsapp2Integration->url_order, $whatsapp2Integration->api_token, $whatsapp2Integration->id);
                $sale             = $event->sale;
                $sale->setRelation('client', $event->client);
                $sale->load('plansSales.plan', 'delivery', 'checkout');
                $domain = Domain::where('status', 3)->where('project_id', $sale->project_id)->first();
                return $whatsapp2Service->sendSale($sale, $sale->plansSales, $domain, 2);
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação para Whatsapp 2.0 na venda ' . $event->sale->id);
            report($e);
        }
    }
}
