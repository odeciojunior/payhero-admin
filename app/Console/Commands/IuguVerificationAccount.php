<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
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
            ->where("gcc.gateway_id", $gatewayId)
            ->whereNotNull("gcc.gateway_subseller_id")
            ->where("gcc.has_charges_webhook", false)
            ->where("cba.transfer_type", "TED")
            ->where("cba.status", "VERIFIED")
            ->get();

        $checkoutGateway = new CheckoutGateway($gatewayId);

        foreach ($companiesCredentials as $row) {
            try {
                $checkoutGateway->createAccount(["companyId" => $row->company_id]);
            } catch (Exception $e) {
                report($e);
            }
        }
    }
}
