<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
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

    public function handle()
    {
        $salesModel = new Sale();

        $sales = $salesModel->with(['project.shopifyIntegrations'])
            ->where('status', $salesModel->present()->getStatus('approved'))
            ->where('payment_method', $salesModel->present()->getPaymentType('credit_card'))
            ->whereNotNull('shopify_order')
            ->whereHas('saleLogs', function ($query) use ($salesModel) {
                $query->where('status_enum', $salesModel->present()->getStatus('in_review'));
            })->get();

        $shopifyStores = [];

        $total = $sales->count();

        $ordersApproved = 0;
        foreach ($sales as $key => $sale) {
            $count = $key + 1;
            $this->line("Verificando venda {$count} de {$total}: {$sale->id}");

            $project = $sale->project;
            if (!empty($shopifyStores[$project->id])) {
                $shopifyService = $shopifyStores[$project->id];
            } else {
                $integration = $sale->project->shopifyIntegrations->first();
                if (!empty($integration)) {
                    $shopifyService = new ShopifyService($integration->url_store, $integration->token, false);
                    $shopifyStores[$project->id] = $shopifyService;
                } else {
                    $this->warn('Nenhuma integração encontrada para este projeto');
                    continue;
                }
            }

            try {
                $order = $shopifyService->getClient()->getOrderManager()->find($sale->shopify_order);
                if ($order->getFinancialStatus() == 'pending') {
                    $data = [
                        "kind" => "capture",
                        "gateway" => "cloudfox",
                        "authorization" => Hashids::connection('sale_id')->encode($sale->id),
                    ];
                    $shopifyService->getClient()->getTransactionManager()->create($sale->shopify_order, $data);
                    $ordersApproved++;

                    $this->info('Order criada no shopify');
                }
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }

        $this->info("{$ordersApproved} orders aprovadas no shopify de {$total} vendas verificadas");
    }
}


