<?php

namespace App\Console\Commands;

use App\Exceptions\CommandMonitorTimeException;
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
            $companiesCredencial = GatewaysCompaniesCredential::where('gateway_id',Gateway::GETNET_PRODUCTION_ID)->where('gateway_status',2)->get();            
            $getnet = new GetnetBackOfficeService();
            $companyService = new CompanyService();

            foreach ($companiesCredencial as $credential) {
                $company = $credential->company;
                if ($company->company_type == 1) { // physical person
                    $result = $getnet->checkPfCompanyRegister($company->document, $company->id);
                } else { // 'juridical person'
                    $result = $getnet->checkPjCompanyRegister($company->document, $company->id);
                }

                $result = json_decode($result);

                if (!empty($result) && !empty($result->subseller_id) && $credential->gateway_subseller_id == $result->subseller_id) {
                    if (($result->status == 'Aprovado Transacionar' || $result->status == 'Aprovado Transacionar e Antecipar') && $result->capture_payments_enabled == 'S') {
                        $credential->update([
                            'gateway_status' => GatewaysCompaniesCredential::GETNET_STATUS_APPROVED // approved
                        ]);

                        $companyService->updateCaptureTransactionEnabled($company);
                    } elseif (($result->status == 'Reprovado' || $result->status == 'Rejeitado') && $result->capture_payments_enabled == 'N') {
                        $credential->update([
                            'gateway_status' => GatewaysCompaniesCredential::GETNET_STATUS_REPROVED // reproved
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
