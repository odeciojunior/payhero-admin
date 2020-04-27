<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ShopifyService;
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

    public function fixPlanProduct()
    {
        //
    }

    public function handle()
    {

        foreach(ShopifyIntegration::all() as $shopifyIntegration){

            try{
                $shopifyService = new ShopifyService($shopifyIntegration->url_store,$shopifyIntegration->token);

                $shopifyService->deleteShopWebhook();

                $shopifyService->createShopWebhook([
                    "topic"   => "products/create",
                    "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($shopifyIntegration->project_id),
                    "format"  => "json",
                ]);

                $shopifyService->createShopWebhook([
                    "topic"   => "products/update",
                    "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($shopifyIntegration->project_id),
                    "format"  => "json",
                ]);

                $shopifyService->createShopWebhook([
                    "topic"   => "orders/updated",
                    "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($shopifyIntegration->project_id) . '/tracking',
                    "format"  => "json",
                ]);

                $this->line($shopifyIntegration->url_store . ' webhooks reiniciados');

            }
            catch(\Exception $e){
                // $this->line($shopifyIntegration->url . ' deu ruim ' . $e->getMessage());
            }

        }
        $this->line('FIM');
    }
}



