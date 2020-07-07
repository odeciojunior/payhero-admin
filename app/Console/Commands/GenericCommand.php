<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\GetnetService;
use Modules\Core\Services\ShopifyService;

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
    protected $signature = 'generic {user?}';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {


        $getnetService = new GetnetService();


        $getnetService->getStatement();


        /*$userId = $this->argument('user');

        if (!empty($userId)) {
            $sales = Sale::with(
                [
                    'upsells',
                    'project.shopifyIntegrations'
                ]
            )->join('sales as s2', 'sales.id', '=', 's2.upsell_id')
                ->where('sales.shopify_order', '!=', DB::raw('s2.shopify_order'))
                ->where('sales.owner_id', $userId)
                ->selectRaw('sales.*')
                ->get();

            $integrations = [];

            foreach ($sales as $sale) {
                $shopifyService = $integrations[$sale->project_id] ?? null;
                if (empty($shopifyService)) {
                    $integration = $sale->project->shopifyIntegrations->first();
                    $shopifyService = new ShopifyService($integration->url_store, $integration->token, false);
                    $integrations[$sale->project_id] = $shopifyService;
                }

                $shopifyService->addItemsToOrder($sale->id);
            }
        }*/
    }
}


