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
        $bankAccountsQuery = DB::table("company_bank_accounts as cba")
            ->select("cba.company_id")
            ->join("companies as c", "cba.company_id", "=", "c.id")
            ->leftJoin("gateways_companies_credentials as gcc", function (JoinClause $join) use ($gatewayId) {
                $join->on("cba.company_id", "=", "gcc.company_id")->where("gcc.gateway_id", $gatewayId);
            })
            ->whereNull("gcc.company_id")
            ->whereNotNull(DB::raw("json_extract(c.situation, '$.company_data')"))
            ->where("cba.transfer_type", "TED")
            ->where("cba.status", "VERIFIED")
            ->orderBy("cba.company_id");

        $progress = $this->getOutput()->createProgressBar($bankAccountsQuery->count());

        $checkoutGateway = new CheckoutGateway($gatewayId);

        $bankAccountsQuery->chunk(60, function ($bankAccounts) use ($checkoutGateway, $progress) {
            foreach ($bankAccounts as $bankAccount) {
                try {
                    $result = $checkoutGateway->createAccount(["companyId" => $bankAccount->company_id]);
                    $this->info(json_encode($result, JSON_PRETTY_PRINT));
                } catch (Exception $e) {
                    $this->error("Error: " . $e->getMessage());
                }

                sleep(1);
                $progress->advance();
            }
        });

        $progress->finish();
    }
}
