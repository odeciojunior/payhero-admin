<?php

namespace App\Console\Commands;

use App\Jobs\ImportShopifyProductsStore;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Core\Services\ShopifyService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $getnet = new GetnetService();
        $company = Company::find(4933);
        $getnet->setCompany($company);
        dd($getnet->getBlockedBalance(),$getnet->getBlockedBalancePending());

    }
}
