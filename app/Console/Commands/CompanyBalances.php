<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBalance;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Services\Gateways\Safe2PayService;

class CompanyBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "company-balance";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command para atualizar o saldo disponivel";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $companies = Company::select([
                "companies.*",
                "us.address_document_status as u_address_document_status",
                "us.personal_document_status as u_personal_document_status",
            ])
                ->join("users as us", "companies.user_id", "=", "us.id")
                ->where("companies.situation->situation_enum", Company::SITUACTION_ACTIVE)
                ->having(
                    DB::Raw("(
                    CASE
                        WHEN company_type = 1 and us.address_document_status = 3 and us.personal_document_status = 3 THEN 3
                        WHEN company_type = 2 and companies.address_document_status = 3 and companies.contract_document_status = 3 THEN 3
                        ELSE false
                    END
                )"),
                    "=",
                    CompanyDocument::STATUS_APPROVED
                )
                ->limit(10);

            foreach ($companies->cursor() as $company) {
                $companyBalance = $this->companyBalance($company);
                $safe2Pay = new Safe2PayService();
                $safe2Pay->setCompany($company);
                $companyBalance->safe_2_pay_available_balance = $safe2Pay->getAvailableBalance() ?? 0;
                $companyBalance->safe_2_pay_pending_balance = $safe2Pay->getPendingBalance() ?? 0;
                $companyBalance->save();
            }
        } catch (Exception $e) {
            dd($e->getMessage());
            report($e);
        }
    }

    private function companyBalance(Company $company)
    {
        $companyBalance = CompanyBalance::where("company_id", $company->id)->get();
        if (empty($companyBalance->id)) {
            $companyBalance = new CompanyBalance();
            $companyBalance->company_id = $company->id;
            return $companyBalance;
        } else {
            dd("xxx");
            return $companyBalance;
        }
    }
}
