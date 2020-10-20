<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\CompanyServiceBraspag;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetBackOfficeService;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    public function handle()
    {
        try {
            $companyModel = new Company();
            $getnet = new GetnetBackOfficeService();

            $companies = $companyModel->whereHas('transactions', function ($query) {
                $query->whereDate('created_at', '>=', '2020-05-01');
            })->whereNull('subseller_getnet_homolog_id')->get();

            foreach ($companies as $company) {
                if ($company->company_type == 1) {
                    $result = $getnet->checkPfCompanyRegister($company->company_document, $company->id);
                } else {
                    $result = $getnet->checkPjCompanyRegister($company->company_document, $company->id);
                }

                if (!empty($result) && !empty(json_decode($result)->subseller_id)) {
                    if (!FoxUtils::isProduction()) {
                        $company->update([
                            'subseller_getnet_homolog_id' => json_decode($result)->subseller_id
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}


