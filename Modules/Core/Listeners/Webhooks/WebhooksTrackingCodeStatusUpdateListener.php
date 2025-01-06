<?php

namespace Modules\Core\Listeners\Webhooks;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Webhook;
use Modules\Webhooks\Services\WebhookService;

/**
 * Class WebhooksTrackingCodeStatusUpdateListener
 * @package Modules\Core\Listeners\Webhooks
 */
class WebhooksTrackingCodeStatusUpdateListener implements ShouldQueue
{
    public $queue = "high";

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
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        try {
            if (!empty($event->trackingId)) {
                $tracking = Tracking::find($event->trackingId);
                $sale = Sale::find($tracking->sale_id);
            } else {
                $sale = !empty($event->sale)
                    ? $event->sale
                    : Sale::find($event->saleId);

                if (
                    empty($sale) ||
                    empty($sale->tracking) ||
                    count($sale->tracking) == 0
                ) {
                    return;
                }
            }

            $tracking = $sale->tracking->last();

            $userProject = UserProject::where(
                "project_id",
                $sale->project_id
            )->first();

            if (empty($userProject)) {
                return;
            }

            $webhook = Webhook::where(
                "company_id",
                $userProject->company_id
            )->first();

            if (!empty($webhook)) {
                $service = new WebhookService($webhook);
                $service->trackingCodeStatusUpdate($tracking);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
