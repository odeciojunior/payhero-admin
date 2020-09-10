<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\User;
use Modules\Core\Services\AwsSns;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\TrackingmoreService;
use Illuminate\Database\Eloquent\Builder;
use Vinkla\Hashids\Facades\Hashids;


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
        /*$users = User::with([
            'companies' => function ($q) {
                $q->where('bank_document_status', 3)
                    ->where('address_document_status', 3)
                    ->where('contract_document_status', 3)
                    ->where('subseller_getnet_id', null);
            }
        ])->whereHas('sales', function (Builder $query) {
            $query->whereDate('created_at', '>=', '2020-05-01');
        })->get();

        $companyService = new CompanyService();
        foreach ($users as $user) {
            $companies = $user->companies;
            foreach ($companies as $company) {
                if (FoxUtils::isEmpty($company->subseller_getnet_id) && !$companyService->verifyFieldsEmpty($company)) {
                    $companyService->createCompanyGetnet($company);
                }
            }
        }

        $this->line('Terminou!!!');*/
    }
}


