<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\CompanyService;

class CreateCompanyGetnet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createCompanyGetnet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {
            $companies = Company::whereDoesntHave('gatewayCompanyCredential', function($q){
                $q->where('gateway_id',Gateway::GETNET_PRODUCTION_ID);
            })
            //->where('bank_document_status', Company::STATUS_APPROVED)
            ->where('address_document_status', Company::STATUS_APPROVED)
            ->where('contract_document_status', Company::STATUS_APPROVED)
            ->get();

            $companyService = new CompanyService();
            foreach ($companies as $company) {
                if ($companyService->verifyFieldsEmpty($company)) {
                    $companyService->createRowCredential($company->id);
                } elseif ($company->present()->getCompanyType($company->company_type) == 'physical person') {
                    $companyService->createCompanyPfGetnet($company);
                } elseif ($company->present()->getCompanyType($company->company_type) == 'juridical person') {
                    $companyService->createCompanyPjGetnet($company);
                }
            }
        } catch (Exception $e) {
            report($e);
        }

    }
}
