<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Support\Facades\Log;

class CreateEmptyCredential extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createEmptyCredential';

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

            $companies = Company::get();
            $total = count($companies);

            $output = new ConsoleOutput();
            $progress = new ProgressBar($output, $total);
            $progress->start();

            foreach ($companies as $company) {
                if (!empty($company->subseller_getnet_id)) {
                    $this->createCredentials(
                        $company,
                        Gateway::GETNET_PRODUCTION_ID,
                        'subseller_getnet_id',
                        'get_net_status',
                        'capture_transaction_enabled'
                    );
                } else {
                    $this->createCredentialsEmpty($company, Gateway::GETNET_PRODUCTION_ID, 0);
                }

                if (!empty($company->subseller_getnet_homolog_id)) {
                    $this->createCredentials(
                        $company,
                        Gateway::GETNET_SANDBOX_ID,
                        'subseller_getnet_homolog_id',
                        'get_net_status'
                    );
                } else {
                    $this->createCredentialsEmpty($company, Gateway::GETNET_SANDBOX_ID, 0);
                }

                if (!empty($company->asaas_id)) {
                    $this->createCredentials($company, Gateway::ASAAS_PRODUCTION_ID, 'asaas_id', null);
                } else {
                    $this->createCredentialsEmpty($company, Gateway::ASAAS_PRODUCTION_ID);
                }

                if (!empty($company->asaas_homolog_id)) {
                    $this->createCredentials($company, Gateway::ASAAS_SANDBOX_ID, 'asaas_homolog_id', null);
                } else {
                    $this->createCredentialsEmpty($company, Gateway::ASAAS_SANDBOX_ID);
                }

                $progress->advance();
            }
            $progress->finish();
            $output->writeln('Fim do command!!');
        }
        catch(Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));
        
    }


    public function createCredentials($company,$gatewayId,$fieldSubseller,$fieldStatus,$fielCaptureTransaction=null,$gatewayApiKey=null){
        try {

            if(\foxutils()->isEmpty($company->gatewayCredential($gatewayId))) {
                GatewaysCompaniesCredential::create(
                    [
                        'company_id' => $company->id,
                        'gateway_id' => $gatewayId,
                        'gateway_status' => !empty($fieldStatus) ? $company->$fieldStatus : GatewaysCompaniesCredential::GATEWAY_STATUS_PENDING,
                        'gateway_subseller_id' => $company->$fieldSubseller,
                        'gateway_api_key' => $gatewayApiKey,
                        'capture_transaction_enabled' => !empty($fielCaptureTransaction) ? $company->$fielCaptureTransaction : null
                    ]
                );
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function createCredentialsEmpty($company,$gatewayId,$captureTransaction = null){

        try {
            if(\foxutils()->isEmpty($company->gatewayCredential($gatewayId))) {
                GatewaysCompaniesCredential::create(
                    [
                        'company_id' => $company->id,
                        'gateway_id' => $gatewayId,
                        'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_PENDING,
                        'capture_transaction_enabled' => $captureTransaction
                    ]
                );
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

}
