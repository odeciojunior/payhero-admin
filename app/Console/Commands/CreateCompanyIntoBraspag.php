<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\CompanyServiceBraspag;
use Modules\Core\Services\FoxUtils;
use Illuminate\Database\Eloquent\Builder;

class CreateCompanyIntoBraspag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CreateCompanyIntoBraspag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    public function handle()
    {
        $companyPresent = (new Company())->present();

        $users = User::with([
            'companies' => function ($q) use ($companyPresent) {
                $q->where('bank_document_status', $companyPresent->getBankDocumentStatus('approved'))
                    ->where('address_document_status', $companyPresent->getAddressDocumentStatus('approved'))
                    ->where('contract_document_status', $companyPresent->getContractDocumentStatus('approved'))
                    ->where('braspag_merchant_id', null);
            }
        ])->whereHas('sales', function (Builder $query) {
            $query->whereDate('created_at', '>=', '2020-05-01');
        })->get();

        $companyServiceBraspag = new CompanyServiceBraspag();

        foreach ($users as $user) {
            foreach ($user->companies as $company) {
                if (FoxUtils::isEmpty($company->braspag_merchant_id)
                    && !(new CompanyService())->verifyFieldsEmptyBraspag($company)
                ) {
                    $companyServiceBraspag->createCompanyBraspag($company);
                }
            }
        }
    }
}
