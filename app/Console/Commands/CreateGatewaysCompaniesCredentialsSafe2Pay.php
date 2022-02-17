<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Models\CompanyDocument;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Illuminate\Support\Facades\Log;

class CreateGatewaysCompaniesCredentialsSafe2Pay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-gateways-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $companies = Company::with('gatewayCompanyCredential')
                ->whereHas('gatewayCompanyCredential', function ($query)  {
                    $query->where('gateway_id', Gateway::GETNET_PRODUCTION_ID);
                })
                ->whereDoesntHave('gatewayCompanyCredential', function ($query)  {
                    $query->where('gateway_id', Gateway::SAFE2PAY_PRODUCTION_ID);
                    $query->where('gateway_id', Gateway::SAFE2PAY_SANDBOX_ID);
                })
                ->withTrashed()
                ->get();

            $total = count($companies);
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach($companies as $company)
            {
                if(!(GatewaysCompaniesCredential::where('company_id', $company->id)
                    ->where('gateway_id', Gateway::SAFE2PAY_PRODUCTION_ID)
                    ->exists())
                ) {
                    $this->createCredentialsEmpty($company->id,Gateway::SAFE2PAY_PRODUCTION_ID);
                }

                if(!(GatewaysCompaniesCredential::where('company_id', $company->id)
                    ->where('gateway_id', Gateway::SAFE2PAY_SANDBOX_ID)
                    ->exists())
                ) {
                    $this->createCredentialsEmpty($company->id,Gateway::SAFE2PAY_SANDBOX_ID);
                }

                $bar->advance();
            }

            $bar->finish();

        } catch (Exception $e) {
            dump($e);
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }


    public function createCredentialsEmpty($companyId,$gatewayId,$captureTransaction = null){

        GatewaysCompaniesCredential::create([
            'company_id'=>$companyId,
            'gateway_id'=>$gatewayId,
            'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_PENDING,
            'capture_transaction_enabled'=> $captureTransaction
        ]);
    }
}
