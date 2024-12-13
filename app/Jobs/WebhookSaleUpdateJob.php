<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Webhook;
use Modules\Webhooks\Services\WebhookService;

/**
 * Class WebhookSaleUpdateJob
 *
 * @package App\Jobs
 */
class WebhookSaleUpdateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The sale instance.
     *
     * @var Sale
     */
    private $sale;

    /**
     * Create a new job instance.
     *
     * @param $saleId
     * @return void
     */
    public function __construct($saleId)
    {
        $this->sale = Sale::find($saleId);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if ($this->sale->project_id) {
                $userProject = UserProject::where("project_id", $this->sale->project_id)->first();
                $companyId = $userProject->company_id ?? null;
            } else {
                $transaction = $this->sale->transactions
                    ->where("type", Transaction::TYPE_PRODUCER)
                    ->where("user_id", $this->sale->owner_id)
                    ->first();
                $companyId = $transaction->company_id ?? null;
            }

            if (empty($companyId)) {
                return;
            }

            $webhooks = Webhook::where("company_id", $companyId)
                ->whereNull("deleted_at")
                ->get();

            if (!empty($webhook)) {
                foreach ($webhooks as $webhook) {
                    $service = new WebhookService($webhook);
                    $service->saleStatusUpdate($this->sale);
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
