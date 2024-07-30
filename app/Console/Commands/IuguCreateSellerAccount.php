<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\Gateways\CheckoutGateway;

class IuguCreateSellerAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "iugu:create-seller-account";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create seller account";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $gatewayId = foxutils()->isProduction() ? Gateway::IUGU_PRODUCTION_ID : Gateway::IUGU_SANDBOX_ID;
        $bankAccounts = DB::table("company_bank_accounts as cba")
            ->select("cba.company_id")
            ->leftJoin("gateways_companies_credentials as gcc", function (JoinClause $join) use ($gatewayId) {
                $join->on("cba.company_id", "=", "gcc.company_id")->where("gcc.gateway_id", $gatewayId);
            })
            ->whereNull("gcc.company_id")
            ->where("cba.transfer_type", "TED")
            ->where("cba.status", "VERIFIED")
            ->where("cba.is_default", 1)
            ->where("cba.deleted_at", null)
            ->get();

        $this->line(count($bankAccounts) . " registros");

        $checkoutGateway = new CheckoutGateway($gatewayId);
        foreach ($bankAccounts as $bankAccount) {
            $this->line("Company " . $bankAccount->company_id);
            $checkoutGateway->createAccount(["companyId" => $bankAccount->company_id]);
        }
    }
}
