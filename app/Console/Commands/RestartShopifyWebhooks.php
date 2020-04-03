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
    protected $signature = 'restartShopifyWebhooks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'restart all webhooks from all shopify integrations in the database';

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

        $integrations = ShopifyIntegration::all();

        $total = $integrations->count();

        foreach ($integrations as $key => $integration) {
            $count = $key + 1;
            $this->info("Loja {$count} de {$total}: $integration->url_store");
            try {
                $shopifyService = new ShopifyService($integration->url_store, $integration->token);

                if (count($shopifyService->getShopWebhook()) != 3) {

                    $this->line('Excluindo webhooks antigos...');
                    $shopifyService->deleteShopWebhook();

                    $this->line('Recriando webhooks...');
                    $this->createShopWebhook([
                        "topic" => "products/create",
                        "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($integration->project_id),
                        "format" => "json",
                    ]);

                    $this->createShopWebhook([
                        "topic" => "products/update",
                        "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($integration->project_id),
                        "format" => "json",
                    ]);

                    $this->createShopWebhook([
                        "topic" => "orders/updated",
                        "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($integration->project_id) . '/tracking',
                        "format" => "json",
                    ]);
                }
            } catch (Exception $e) {
                $this->error('ERRO: ' . $e->getMessage());
            }
        }

        $this->info('ACABOOOOOOOOOU!');
    }

}

