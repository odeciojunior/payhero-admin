<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\User;
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
            $users = User::with([
                'companies' => function ($q) {
                    $q->where('bank_document_status', 3)
                        ->where('address_document_status', 3)
                        ->where('contract_document_status', 3)
                        ->whereNull('subseller_getnet_id');
                }
            ])->get();

            $companyService = new CompanyService();
            foreach ($users as $user) {
                $companies = $user->companies;
                foreach ($companies as $company) {
                    if ($companyService->verifyFieldsEmpty($company)) {
                        $company->update([
                            'get_net_status' => $company->present()->getStatusGetnet('pending'),
                        ]);
                    } elseif ($company->present()->getCompanyType($company->company_type) == 'physical person') {
                        $companyService->createCompanyPfGetnet($company);
                    } elseif ($company->present()->getCompanyType($company->company_type) == 'juridical person') {
                        $companyService->createCompanyPjGetnet($company);
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
