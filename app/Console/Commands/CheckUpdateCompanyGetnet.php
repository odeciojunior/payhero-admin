<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
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
            $companies = Company::where('get_net_status', 2)->get();
            $getnet = new GetnetBackOfficeService();
            $companyService = new CompanyService();

            foreach ($companies as $company) {
                if ($company->company_type == 1) { // physical person
                    $result = $getnet->checkPfCompanyRegister($company->company_document, $company->id);
                } else { // 'juridical person'
                    $result = $getnet->checkPjCompanyRegister($company->company_document, $company->id);
                }

                $result = json_decode($result);

                if (!empty($result) && !empty($result->subseller_id) && $company->subseller_getnet_id == $result->subseller_id) {
                    if ($result->enabled == 'S' && ($result->status == 'Aprovado Transacionar' || $result->status == 'Aprovado Transacionar e Antecipar') && $result->capture_payments_enabled == 'S') {
                        $company->update([
                            'get_net_status' => 1 // approved
                        ]);
                    } elseif (($result->status == 'Reprovado' || $result->status == 'Rejeitado') && $result->capture_payments_enabled == 'N') {
                        $company->update([
                            'get_net_status' => 3 // reproved
                        ]);
                    }
                }
                $companyService->updateCaptureTransactionEnabled($company);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
