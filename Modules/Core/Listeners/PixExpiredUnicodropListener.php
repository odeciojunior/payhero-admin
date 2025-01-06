<?php

namespace Modules\Core\Listeners;

use Exception;
use Modules\Core\Services\UnicodropService;
use Modules\Core\Entities\UnicodropIntegration;

class PixExpiredUnicodropListener
{
    public function handle($event): void
    {
        try {
            $sale = $event->sale;
            if ($sale->api_flag) {
                return;
            }

            $unicodropIntegration = UnicodropIntegration::query()
                ->where("project_id", $sale->project_id)
                ->where("pix", 1)
                ->first();

            if (!empty($unicodropIntegration)) {
                $unicodropService = new UnicodropService($unicodropIntegration);
                $unicodropService->pixExpired($sale);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
