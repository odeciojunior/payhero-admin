<?php

namespace Modules\Core\Listeners;

use Exception;
use Modules\Core\Entities\HotbilletIntegration;
use Modules\Core\Events\PixExpiredEvent;

use Modules\Core\Services\HotBilletService;

use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class HotBilletPixExpiredListener
 * @package App\Listeners
 */
class HotBilletPixExpiredListener implements ShouldQueue
{
    public $queue = "default";

    /**
     * HotBilletPixExpiredListener constructor.
     */
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

            $hotbilletIntegration = (new HotbilletIntegration())
                ->newQuery()
                ->where("project_id", $sale->project_id)
                ->where("pix_expired", true)
                ->first();

            if (!empty($hotbilletIntegration)) {
                $hotbilletService = new HotBilletService($hotbilletIntegration->link);
                $hotbilletService->pixExpired($sale);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
