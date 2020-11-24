<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Services\BraspagBackOfficeService;
use Modules\Core\Services\FoxUtils;

class UpdateStatusCompanyBraspag extends Command
{
    protected $signature = 'command:UpdateStatusCompanyBraspag';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $companyModel = new Company();

//        if (FoxUtils::isProduction()) {
//            $companies = $companyModel->whereNotNull('')->get();
//        } else {
//            $companies = $companyModel->whereNotNull('')->get();
//        }

        $braspagBackOffice = new BraspagBackOfficeService();

//        foreach ($companies as $company) {
//            if (FoxUtils::isProduction()) {
//                $companyMerchantId = ;
//            } else {
//                $companyMerchantId = ;
//            }

//            $response = $braspagBackOffice->checkCompanyRegister($companyMerchantId, $company->id);

//            if (!empty($response) && !empty(json_decode($response)->Analysis->Status) && json_decode($response)->Analysis->Status != $companyModel->present()->getStatusBraspag($company->braspag_status)) {
//                $braspagNewStatus = $companyModel->present()->getStatusBraspag(json_decode($response)->Analysis->Status);
//
//                if (!empty($braspagNewStatus) && $companyMerchantId == json_decode($response)->MerchantId) {
//                    $company->update([
//                        'braspag_status' => $braspagNewStatus
//                    ]);
//                }
//            }
//        }

        $this->line('Terminou');
    }
}
