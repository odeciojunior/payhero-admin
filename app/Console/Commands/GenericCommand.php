<?php

namespace App\Console\Commands;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ShopifyService;

class GenericCommand extends Command
{
    protected $signature = "generic {name?}";
    protected $description = "Command description";
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $stores = Project::whereHas("shopifyIntegrations")
            ->with("shopifyIntegrations")
            ->orderBy("id", "desc")
            ->get();
        foreach ($stores as $key => $store) {
            try {
                if ($key == 92 || $key == 137) {
                    continue;
                }
                $shopifyService = new ShopifyService(
                    $store->shopifyIntegrations()->first()->url_store,
                    $store->shopifyIntegrations()->first()->token
                );
                $this->line("---------------------------");
                $this->line("====> " . $key . " - " . $store->name);
                $this->line("====> " . $key . " - " . $store->created_at);
                // $shopifyService->deleteShopWebhook();
                // $shopifyService->createShopifyIntegrationWebhook(
                //     $store->id,
                //     "https:admin.azcend.vip/postback/shopify/"
                // );
                $webhooks = $shopifyService->getShopWebhook();
                foreach ($webhooks as $webhook) {
                    $this->line("webhook: " . $webhook->getTopic());
                }
            } catch (Exception $e) {
                continue;
            }
        }
        $this->line($stores);
    }
}
