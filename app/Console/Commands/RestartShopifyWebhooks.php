<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\ShopifyService;

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
        $shopifyIntegrations = ShopifyIntegration::where("status", ShopifyIntegration::STATUS_APPROVED)
            ->whereHas("project", function ($query) {
                $query->where("status", Project::STATUS_ACTIVE);
            })
            ->get();

        foreach ($shopifyIntegrations as $shopifyIntegration) {
            try {
                $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                $shopifyService->createShopifyIntegrationWebhook(
                    $shopifyIntegration->project_id,
                    "https://admin.azcend.com.br/postback/shopify/",
                );

                $this->info("Webhook created for project {$shopifyIntegration->project_id}");
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }
}
