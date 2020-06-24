<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Services\GetnetService;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generic {user?}';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $getNetService = new GetnetService();

        /**
         * Lorram
         */
//        $company = Company::find(13);
        /** Joao */
        $company = Company::find(28);


//        $getNetService->createPfCompany($company);

//        $getNetService->createPjCompany($company);
//        $getNetService->complementPjCompany($company);
//        $getNetService->checkComplementPjCompanyRegister($company->company_document);
//        $getNetService->checkPjCompanyRegister($company->company_document);
        $getNetService->checkPfCompanyRegister($company->company_document);
    }
}
