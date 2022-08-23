<?php

namespace Modules\Core\Listeners\Webhooks;

use Exception;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Webhook;
use Modules\Core\Services\WebhookService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class WebhooksSaleStatusChangeListener
 * @package Modules\Core\Listeners\Webhooks
 */
class WebhooksSaleStatusChangeListener implements ShouldQueue
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
            $sale = $event->sale;
            $checkout = $event->sale->checkout;
            $checkout->load("project");

            $userProject = UserProject::where(
                "project_id",
                $checkout->project_id
            )->first();

            if (empty($userProject)) {
                return;
            }

            $webhook = Webhook::where(
                "company_id",
                $userProject->company_id
            )->first();

            if (!empty($webhook)) {
                $service = new WebhookService($webhook->url);
                $service->saleStatusChange($sale);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
