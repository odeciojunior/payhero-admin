<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\CompanyBankAccount;
use Illuminate\Support\Str;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\ShopifyService;

class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "generic {name?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         $integrationId = 1888;
         $productId = 5327529050272;

         $shopifyIntegration = ShopifyIntegration::find($integrationId);

        $integration = ShopifyIntegration::find($integrationId);
        $service = new ShopifyService($integration->url_store, $integration->token, false);

        // $result = $service
        //     ->getClient()
        //     ->createRequest("GET", "https://{$integration->url_store}/admin/api/2022-04/products/{$productId}.json");

        // dd($result["product"]["status"]);


        $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
        //$shopifyService->importShopifyStore($shopifyIntegration->project->id, $shopifyIntegration->userId);

        $shopifyService->importShopifyProduct($shopifyIntegration->project->id, $shopifyIntegration->userId, $productId );
    }
}
