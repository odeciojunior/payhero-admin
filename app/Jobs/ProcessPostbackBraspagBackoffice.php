<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Company;
use Modules\Core\Services\CompanyServiceBraspag;
use Modules\Core\Services\FoxUtils;

class ProcessPostbackBraspagBackoffice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $merchantId;
    private string $status;


    public function __construct(string $merchantId, string $status)
    {
        $this->merchantId = $merchantId;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (!FoxUtils::isProduction()) {
                return;
            }

            $companyModel = new Company();

//            $company = $companyModel->where('', $this->merchantId)->first();

            if (empty($company) || $companyModel->present()->getStatusBraspag($company->braspag_status) == $this->status) {
                return;
            }

            $company->update([
                "braspag_status" => $companyModel->present()->getStatusBraspag($this->status)
            ]);
        } catch (Exception $e) {
            report($e);
        }
    }
}
