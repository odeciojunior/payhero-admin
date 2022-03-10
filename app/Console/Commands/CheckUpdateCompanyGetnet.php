<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;

class CheckUpdateCompanyGetnet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:checkUpdateCompanyGetnet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza status da getnet da empresas cadastradas';

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
            $companiesCredencial = GatewaysCompaniesCredential::where('gateway_id',Gateway::GETNET_PRODUCTION_ID)->where('gateway_status', GatewaysCompaniesCredential::GATEWAY_STATUS_REVIEW)->get();
            $getnet = new GetnetBackOfficeService();
            $companyService = new CompanyService();

            foreach ($companiesCredencial as $credential) {
                $company = $credential->company;

                if (!foxutils()->isEmpty($company)) {
                    if ($company->company_type == Company::PHYSICAL_PERSON) { // physical person
                        $result = $getnet->checkPfCompanyRegister($company->document, $company->id);
                    } else { // 'juridical person'
                        $result = $getnet->checkPjCompanyRegister($company->document, $company->id);
                    }

                    $result = json_decode($result);

                    if (!empty($result->subseller_id) && $credential->gateway_subseller_id == $result->subseller_id) {
                        switch ($result->status) {
                            case 'Aprovado Transacionar':
                            case 'Aprovado Transacionar e Antecipar':
                                if ($result->capture_payments_enabled == 'S') {
                                    $credential->update(
                                        [
                                            'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED
                                            // approved
                                        ]
                                    );

                                    $companyService->updateCaptureTransactionEnabled($company);
                                }
                                break;
                            case 'Reprovado':
                            case 'Rejeitado':
                                if ($result->capture_payments_enabled == 'N') {
                                    $credential->update(
                                        [
                                            'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_REPROVED
                                            // reproved
                                        ]
                                    );
                                }
                                break;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }

    }
}
