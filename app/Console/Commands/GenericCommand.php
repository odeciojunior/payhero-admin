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
        $shopifyIntegrationModel = new ShopifyIntegration();
        $sales = Sale::whereHas('project.shopifyIntegrations', function ($query) {
            $query->where('status', 2);
        })
            ->whereBetween('end_date', ['2020-04-21 00:00:00', '2020-04-30 23:59:59'])
            ->where('status', 1)
            ->where('payment_method', 2)
            ->orderBy('id', 'desc')
            ->get();
        $x = 1;
        foreach ($sales as $sale) {
            $shopifyIntegration = $shopifyIntegrationModel->where('project_id', $sale->project_id)->first();
            try {
                $this->line($x++ . "-> Atualizando pedido no shopify " . $sale->id);
                $credential = new PublicAppCredential($shopifyIntegration->token);
                $client = new Client($credential, $shopifyIntegration->url_store, [
                    'metaCacheDir' => '/var/tmp',
                ]);
                $client->getTransactionManager()->create($sale->shopify_order, [
                    "kind" => "sale",
                    "source" => "external",
                    "gateway" => "cloudfox",
                    "authorization" => Hashids::connection('sale_id')->encode($sale->id),
                ]);
            } catch (Exception $e) {
                $this->line("Erro ao atualizar pedido no shopify " . $sale->id . ' erro ' . $e->getMessage());
            }
            try {
                $this->line('Gerando pedido na venda ' . $sale->id);
                $shopifyIntegration = $sale->project->shopifyIntegrations->first();
                $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                $shopifyService->newOrder($sale);
            } catch (Exception $e) {
                $this->line('Erro ao gerar pedido na venda ' . $sale->id . ' Erro: ' . $e->getMessage());
            }
        }
    }
}


