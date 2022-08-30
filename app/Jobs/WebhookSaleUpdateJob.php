<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Webhook;
use Modules\Core\Services\WebhookService;

/**
 * Class WebhookSaleUpdateJob
 *
 * @package App\Jobs
 */
class WebhookSaleUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The sale instance.
     *
     * @var Sale
     */
    private $sale;

    /**
     * Create a new job instance.
     *
     * @param Sale $sale
     * @return void
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $checkout = $this->sale->checkout;
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
                $service = new WebhookService($webhook);
                $service->saleStatusUpdate($this->sale);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
