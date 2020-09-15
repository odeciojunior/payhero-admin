<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;

class UpdateCompanyGetnet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateCompanyGetnet';

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

            foreach ($companies as $company) {
                if ($company->company_type == 1) {
                    // physical person
                    $result = $getnet->checkPfCompanyRegister($company->company_document, $company->id);
                } else {
                    // 'juridical person'
                    $result = $getnet->checkPjCompanyRegister($company->company_document, $company->id);
                }

                $result = json_decode($result);

                if (!empty($result) && $company->subseller_getnet_id == $result->subseller_id) {
                    if ($result->enabled == 'S' && $result->status == 'Aprovado Transacionar' && $result->capture_payments_enabled == 'S') {
                        $company->update([
                            'get_net_status' => 4 // approved
                        ]);
                    } elseif ($result->enabled == 'N' && $result->status == 'Reprovado' && $result->capture_payments_enabled == 'N') {
                        $company->update([
                            'get_net_status' => 3 // reproved
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
