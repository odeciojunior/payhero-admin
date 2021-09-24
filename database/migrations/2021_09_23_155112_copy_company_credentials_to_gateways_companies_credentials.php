<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;

class CopyCompanyCredentialsToGatewaysCompaniesCredentials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = Company::get();
        $total = count($companies);

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $total);
        $progress->start();

        foreach($companies as $company)
        {

            if(!empty($company->subseller_getnet_id)){
                $this->createCredentials($company->id,Gateway::GETNET_PRODUCTION_ID,$company->get_net_status,$company->subseller_getnet_id);
            }

            if(!empty($company->subseller_getnet_homolog_id)){
                $this->createCredentials($company->id,Gateway::GETNET_SANDBOX_ID,$company->get_net_status,$company->subseller_getnet_homolog_id);
            }

            if(!empty($company->asaas_id)){
                $this->createCredentials($company->id,Gateway::ASAAS_PRODUCTION_ID,null,$company->asaas_id);
            }

            if(!empty($company->asaas_homolog_id)){
                $this->createCredentials($company->id,Gateway::ASAAS_PRODUCTION_ID,null,$company->asaas_homolog_id);
            }
            $progress->advance();
        }
        $progress->finish();    
        $output->writeln('');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("TRUNCATE gateways_companies_credentials;");
    }

    public function createCredentials($companyId,$gatewayId,$gatewayStatus,$gatewaySubsellerId,$gatewayApiKey=null){
        GatewaysCompaniesCredential::create([
            'company_id'=>$companyId,
            'gateway_id'=>$gatewayId,
            'gateway_status'=>$gatewayStatus,
            'gateway_subseller_id'=>$gatewaySubsellerId,
            'gateway_api_key'=>$gatewayApiKey
        ]);
    }
}
