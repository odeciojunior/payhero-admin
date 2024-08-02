<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\Gateways\CheckoutGateway;

class MalgaCreateSellerAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "malga:create-seller-account";

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
        $gatewayId = foxutils()->isProduction() ? Gateway::MALGA_PRODUCTION_ID : Gateway::MALGA_SANDBOX_ID;
        $bankAccounts = DB::table("company_bank_accounts as cba")
            ->select("cba.company_id")
            ->leftJoin("gateways_companies_credentials as gcc", function (JoinClause $join) use ($gatewayId) {
                $join->on("cba.company_id", "=", "gcc.company_id")->where("gcc.gateway_id", $gatewayId);
            })
            ->whereNull("gcc.company_id")
            ->where("cba.transfer_type", "TED")
            ->where("cba.status", "VERIFIED")
            ->get();

        $progress = $this->getOutput()->createProgressBar(count($bankAccounts));

        $checkoutGateway = new CheckoutGateway($gatewayId);
        foreach ($bankAccounts as $bankAccount) {
            try {
                $result = $checkoutGateway->createAccount(["companyId" => $bankAccount->company_id]);
                $this->info(json_encode($result, JSON_PRETTY_PRINT));
            } catch (Exception $e) {
                $this->error("Error: " . $e->getMessage());
            }

            $progress->advance();
        }

        $progress->finish();
    }
}
