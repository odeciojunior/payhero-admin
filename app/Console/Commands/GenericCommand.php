<?php

namespace App\Console\Commands;

use Modules\Core\Services\CompanyService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $this->updateCaptureTransactionEnabled();
    }

    public function updateCaptureTransactionEnabled(): void
    {
        $companyService = new CompanyService();
        foreach (Company::all() as $company) {
            $this->line("company id = " . $company->id . " getSubsellerId = " . $companyService->getSubsellerId( $company) . " getGatewaySubsellerId = " . $company->getGatewaySubsellerId(Gateway::GETNET_PRODUCTION_ID) );
        }
    }
}
