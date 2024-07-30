<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Services\Gateways\CheckoutGateway;

class IuguVerificationAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "iugu:verification-account";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $gatewayId = foxutils()->isProduction() ? Gateway::IUGU_PRODUCTION_ID : Gateway::IUGU_SANDBOX_ID;
        $companiesCredentials = DB::table("gateways_companies_credentials as gcc")
            ->select("gcc.company_id")
            ->join("company_bank_accounts as cba", "gcc.company_id", "=", "cba.company_id")
            ->join("companies as c", "gcc.company_id", "=", "c.id")
            ->where("gcc.gateway_id", $gatewayId)
            ->where("gcc.gateway_status", GatewaysCompaniesCredential::GATEWAY_STATUS_PENDING)
            ->whereNotNull("gcc.gateway_subseller_id")
            ->whereNull("gcc.gateway_contact_id")
            ->whereNotNull("c.zip_code")
            ->whereNotNull("c.street")
            ->where("cba.is_default", true)
            ->whereNull("cba.deleted_at")
            ->get();

        $this->line(count($companiesCredentials) . " registros");

        $checkoutGateway = new CheckoutGateway($gatewayId);

        foreach ($companiesCredentials as $row) {
            $this->line("Company " . $row->company_id);
            try {
                $checkoutGateway->verificationAccount(["companyId" => $row->company_id]);
            } catch (Exception $e) {
                report($e);
            }
        }
    }
}
