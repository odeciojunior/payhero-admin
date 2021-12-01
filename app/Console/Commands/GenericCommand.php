<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\Gateways\AsaasService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {

    }

}
