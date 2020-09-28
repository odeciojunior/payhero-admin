<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

//use Modules\Core\Entities\Project;//
//use Modules\Core\Entities\Tracking;//
use Modules\Core\Entities\User;

//use Modules\Core\Services\AwsSns;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\CompanyServiceBraspag;

//CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\BraspagBackOfficeService;

//GetnetBackOfficeService;
//use Modules\Core\Services\TrackingmoreService;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Entities\Company;
use Vinkla\Hashids\Facades\Hashids;

//use Modules\Core\Services\BraspagService;

class BraspagServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BraspagService';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    public function handle()
    {
        $users = User::with([
            'companies' => function ($q) {
                $q->where('bank_document_status', 3)
                    ->where('address_document_status', 3)
                    ->where('contract_document_status', 3)
                    ->where('braspag_merchant_id', null)
                    ->where('braspag_merchant_homolog_id', null);
            }
        ])->whereHas('sales', function (Builder $query) {
            $query->whereDate('created_at', '>=', '2020-05-01');
        })->get();

        $companyServiceBraspag = new CompanyServiceBraspag();

        foreach ($users as $user) {
            foreach ($user->companies as $company) {
                $this->line("Tentando cadastrar empresa {$company->fantasy_name}");

                if ((FoxUtils::isEmpty($company->braspag_merchant_id) || FoxUtils::isEmpty($company->braspag_merchant_homoloh_id))
                    && !(new CompanyService())->verifyFieldsEmpty($company)
                ) {
                    $companyServiceBraspag->createCompanyBraspag($company);
                }
            }
        }

        $this->line('Terminou!!!');
    }
}
