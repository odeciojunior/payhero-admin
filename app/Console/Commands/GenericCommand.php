<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\ShopifyService;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    public function handle()
    {
        $sales = Sale::with('project.shopifyIntegrations')
            ->where('status', 3)
            ->where('payment_method', 1)
            ->whereDate('start_date', '>=', now()->subDays(30)->startOfDay())
            ->whereNotNull('shopify_order')
            ->get();

        $total = $sales->count();
        $count = 0;

        $integrations = [];
        foreach ($sales as $sale) {
            try {
                $count++;
                $this->line("Venda {$count} de {$total}: {$sale->id}");

                if (empty($integrations[$sale->project_id])) {
                    $integration = $sale->project->shopifyIntegrations->first();
                    $integrations[$sale->project_id] = new ShopifyService($integration->url_store,
                        $integration->token,
                        false);
                }
                $shopifyService = $integrations[$sale->project_id];

                $order = $shopifyService->getClient()->getOrderManager()->find($sale->shopify_order);

                $fulfillments = $order->getFulfillments();
                foreach ($fulfillments as $fulfillment) {
                    $shopifyService->getClient()->getFulfillmentManager()->cancel($order->getId(),
                        $fulfillment->getId());
                }
                $shopifyService->getClient()->getOrderManager()->cancel($order->getId());

                $sale->shopify_order = null;
                $sale->save();

                $this->line('Foi!');
            } catch (Exception $e) {
                $this->error('ERR: ' . $e->getMessage());
            }
        }
    }
}


