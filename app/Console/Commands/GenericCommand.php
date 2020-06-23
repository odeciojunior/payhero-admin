<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\GetnetBackofficeRequests;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetService;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\SmsService;
use Modules\Core\Services\TrackingService;

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
    protected $signature = 'generic';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function handle()
    {
        $getNetService = new GetnetService();

        $company = Company::find(13);

//        $getNetService->createPjCompany($company);
//        $getNetService->complementPjCompany($company);
//        $getNetService->checkComplementPjCompanyRegister($company->company_document);
        $getNetService->checkPjCompanyRegister($company->company_document);
    }

}
