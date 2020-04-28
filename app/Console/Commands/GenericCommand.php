<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Core\Entities\AnticipatedTransaction;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ShopifyService;
use Slince\Shopify\Client;
use Slince\Shopify\PublicAppCredential;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generic';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {


    }

}


