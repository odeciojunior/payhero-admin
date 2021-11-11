<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
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
        DB::statement('update customers set asaas_buyer_id = null');
    }

}
