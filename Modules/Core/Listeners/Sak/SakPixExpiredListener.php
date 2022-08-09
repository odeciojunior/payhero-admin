<?php

namespace Modules\Core\Listeners\Sak;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Core\Services\Whatsapp2Service;

class SakPixExpiredListener implements ShouldQueue
{
    use Queueable;

    public function handle($event)
    {
        try {
            $whatsapp2Integration = Whatsapp2Integration::where("project_id", $event->sale->project_id)
                ->where("pix_expired", 1)
                ->first();

            if (!empty($whatsapp2Integration)) {
                $whatsapp2Service = new Whatsapp2Service(
                    $whatsapp2Integration->url_checkout,
                    $whatsapp2Integration->url_order,
                    $whatsapp2Integration->api_token,
                    $whatsapp2Integration->id
                );

                $whatsapp2Service->sendPixSaleExpired($event->sale);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
