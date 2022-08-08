<?php

namespace Modules\Core\Listeners;

use Exception;
use Modules\Core\Entities\NotificacoesInteligentesIntegration;
use Modules\Core\Events\PixExpiredEvent;

use Modules\Core\Services\NotificacoesInteligentesService;

use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class NotificacoesInteligentesPixExpiredListener
 * @package App\Listeners
 */
class NotificacoesInteligentesPixExpiredListener implements ShouldQueue
{
    public $queue = "default";

    /**
     * NotificacoesInteligentesPixExpiredListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $event
     */
    public function handle(PixExpiredEvent $event)
    {
        try {
            $sale = $event->sale;

            $integration = NotificacoesInteligentesIntegration::where("project_id", $sale->project_id)
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
