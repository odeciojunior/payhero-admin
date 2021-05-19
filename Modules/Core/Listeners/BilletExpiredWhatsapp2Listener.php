<?php


namespace Modules\Core\Listeners;


use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Core\Events\BilletExpiredEvent;
use Modules\Core\Services\Whatsapp2Service;

class BilletExpiredWhatsapp2Listener implements ShouldQueue
{
    use Queueable;

    public function handle(BilletExpiredEvent $event)
    {
        try {
            $whatsapp2Integration = Whatsapp2Integration::where('project_id', $event->sale->project_id)
                ->where('billet_paid', 1)
                ->first();

            if (!empty($whatsapp2Integration)) {
                $whatsapp2Service = new Whatsapp2Service($whatsapp2Integration->url_checkout, $whatsapp2Integration->url_order, $whatsapp2Integration->api_token, $whatsapp2Integration->id);
                $sale = $event->sale;
                $sale->setRelation('customer', $event->sale->customer);
                $sale->load('plansSales.plan', 'delivery', 'checkout');
                $domain = Domain::where('status', 3)->where('project_id', $sale->project_id)->first();
                return $whatsapp2Service->sendSale($sale, $sale->plansSales, $domain, 4);
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação para Whatsapp 2.0 boleto expirado' . $event->sale->id);
            report($e);
        }
    }
}
