<?php

namespace App\Console\Commands;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\User;
use Modules\Core\Services\Gateways\CheckoutGateway;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $service = new CheckoutGateway(Gateway::ASAAS_PRODUCTION_ID);
        dd($service->getCurrentBalance(3442));
    }

}
