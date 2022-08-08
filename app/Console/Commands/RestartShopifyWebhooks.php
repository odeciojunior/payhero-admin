<?php

namespace App\Console\Commands;

use Exception;
use Hashids\Hashids;
use Illuminate\Console\Command;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Entities\ShopifyIntegration;

class RestartShopifyWebhooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "restartShopifyWebhooks";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "restart all webhooks from all shopify integrations in the database";

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
        foreach (ShopifyIntegration::all() as $shopifyIntegration) {
            try {
                $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                $shopifyService->deleteShopWebhook();

                $shopifyService->createShopWebhook([
                    "topic" => "products/create",
                    "address" =>
                        "https://sirius.cloudfox.net/postback/shopify/" .
                        Hashids::encode($shopifyIntegration->project_id),
                    "format" => "json",
                ]);

                $shopifyService->createShopWebhook([
                    "topic" => "products/update",
                    "address" =>
                        "https://sirius.cloudfox.net/postback/shopify/" .
                        Hashids::encode($shopifyIntegration->project_id),
                    "format" => "json",
                ]);

                $shopifyService->createShopWebhook([
                    "topic" => "orders/updated",
                    "address" =>
                        "https://sirius.cloudfox.net/postback/shopify/" .
                        Hashids::encode($shopifyIntegration->project_id) .
                        "/tracking",
                    "format" => "json",
                ]);
            } catch (Exception $e) {
                //
            }
        }
    }
}
