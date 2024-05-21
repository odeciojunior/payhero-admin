<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\Shopify\Client;
use Modules\Core\Services\Shopify\WebhookService;

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
        $shopifyIntegration = ShopifyIntegration::find(2);

        $client = new Client($shopifyIntegration->url_store, $shopifyIntegration->token);
        $webhookService = new WebhookService($client);

        $webhooks = $webhookService->findAll();

        dump($webhooks);
    }
}
