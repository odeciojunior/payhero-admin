<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\Gateways\CheckoutGateway;

class CheckPayupChargebacks extends Command
{
    protected $signature = "check:payup:chargbacks";

    protected $description = "Check Payup Chargebacks";

    public function handle()
    {
        $checkoutGateway = new CheckoutGateway(Gateway::PAYUP_PRODUCTION_ID);
        $checkoutGateway->checkPayupChargebacks();
    }
}
