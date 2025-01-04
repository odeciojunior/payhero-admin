<?php

namespace Modules\Core\Listeners;

use Exception;
use Modules\Core\Entities\NotificacoesInteligentesIntegration;
use Modules\Core\Events\PixExpiredEvent;

use Modules\Core\Services\NotificacoesInteligentesService;

use Illuminate\Contracts\Queue\ShouldQueue;

class NotificacoesInteligentesPixExpiredListener implements ShouldQueue
{
    public string $queue = "default";

    public function __construct()
    {
        //
    }

    public function handle(PixExpiredEvent $event): void
    {
        try {
            $sale = $event->sale;
            if ($sale->api_flag) {
                return;
            }

            $integration = NotificacoesInteligentesIntegration::query()
                ->where("project_id", $sale->project_id)
                ->where("pix_expired", true)
                ->first();

            if (!empty($integration)) {
                $service = new NotificacoesInteligentesService($integration->link);
                $service->pixExpired($sale);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
