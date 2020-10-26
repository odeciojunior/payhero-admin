<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Services\CompanyService;

class UpdateCaptureTransactionEnabled extends Command
{
    protected $signature = 'command:updateCaptureTransactionEnabled';

    protected $description = 'update flag capture transaction enabled true or false';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $companyService = new CompanyService();

        $companies = Company::whereNotNull('subseller_getnet_id')->get();

        foreach ($companies as $company) {
            $companyService->updateCaptureTransactionEnabled($company);
        }
        $this->line('Terminou');
    }
}
