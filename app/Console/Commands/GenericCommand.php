<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\PromotionalTax;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\UserService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $promotional_taxes = PromotionalTax::where('old_tax', 'like', '%3.9%')
            ->withTrashed()
            ->get();

        foreach ($promotional_taxes as $promotional_tax) {
            (new UserService())->removePromotionalTax($promotional_tax);
        }
    }

}
