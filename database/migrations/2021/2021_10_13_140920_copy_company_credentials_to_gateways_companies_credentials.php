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
        DB::statement("TRUNCATE gateways_companies_credentials;");

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
                    "subseller_getnet_id",
                    "get_net_status",
                    "capture_transaction_enabled"
                );
            } else {
                $this->createCredentialsEmpty($company, Gateway::GETNET_PRODUCTION_ID, 0);
            }

            if (!empty($company->subseller_getnet_homolog_id)) {
                $this->createCredentials(
                    $company,
                    Gateway::GETNET_SANDBOX_ID,
                    "subseller_getnet_homolog_id",
                    "get_net_status"
                );
            } else {
                $this->createCredentialsEmpty($company, Gateway::GETNET_SANDBOX_ID, 0);
            }

            if (!empty($company->asaas_id)) {
                $this->createCredentials($company, Gateway::ASAAS_PRODUCTION_ID, "asaas_id", null);
            } else {
                $this->createCredentialsEmpty($company, Gateway::ASAAS_PRODUCTION_ID);
            }

            if (!empty($company->asaas_homolog_id)) {
                $this->createCredentials($company, Gateway::ASAAS_SANDBOX_ID, "asaas_homolog_id", null);
            } else {
                $this->createCredentialsEmpty($company, Gateway::ASAAS_SANDBOX_ID);
            }

            $progress->advance();
        }
        $progress->finish();
        $output->writeln("");
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

    public function createCredentials(
        $company,
        $gatewayId,
        $fieldSubseller,
        $fieldStatus,
        $fielCaptureTransaction = null,
        $gatewayApiKey = null
    ) {
        GatewaysCompaniesCredential::create([
            "company_id" => $company->id,
            "gateway_id" => $gatewayId,
            "gateway_status" => !empty($fieldStatus)
                ? $company->$fieldStatus
                : GatewaysCompaniesCredential::GATEWAY_STATUS_PENDING,
            "gateway_subseller_id" => $company->$fieldSubseller,
            "gateway_api_key" => $gatewayApiKey,
            "capture_transaction_enabled" => !empty($fielCaptureTransaction) ? $company->$fielCaptureTransaction : null,
        ]);
    }

    public function createCredentialsEmpty($company, $gatewayId, $captureTransaction = null)
    {
        GatewaysCompaniesCredential::create([
            "company_id" => $company->id,
            "gateway_id" => $gatewayId,
            "gateway_status" => GatewaysCompaniesCredential::GATEWAY_STATUS_PENDING,
            "capture_transaction_enabled" => $captureTransaction,
        ]);
    }
}
