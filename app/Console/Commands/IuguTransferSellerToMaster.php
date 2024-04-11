<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\Gateways\CheckoutGateway;

class IuguTransferSellerToMaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "iugu:transfer-seller-to-master";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "transfere saldo da subconta para a conta principal";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = DB::table("companies as c")
            ->select("c.id", "c.vega_balance")
            ->join("gateways_companies_credentials as cgc", "c.id", "=", "cgc.company_id")
            ->where("c.vega_balance", ">", 0)
            ->where("cgc.gateway_id", Gateway::IUGU_PRODUCTION_ID)
            ->where("cgc.has_charges_webhook", true)
            ->get();
        $this->line(count($companies) . " registros");

        $checkoutGateway = new CheckoutGateway(Gateway::IUGU_PRODUCTION_ID);
        foreach ($companies as $company) {
            $checkoutGateway->transferAccountToMainAccount($company->id, 0, "");
        }
    }
}
