<?php

namespace Modules\Core\Listeners;

use Exception;
use Modules\Core\Services\UnicodropService;
use Modules\Core\Entities\UnicodropIntegration;

class PixExpiredUnicodropListener
{
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        try {
            $unicodropIntegration = UnicodropIntegration::where('project_id', $event->sale->project_id)
                                                        ->where('pix', 1)
                                                        ->first();

            if (!empty($unicodropIntegration)) {
                $unicodropService = new UnicodropService($unicodropIntegration);
                $unicodropService->pixExpired($event->sale);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
