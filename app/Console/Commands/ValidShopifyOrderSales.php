<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\ShopifyService;

class ValidShopifyOrderSales extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'command:ValidShopifyOrderSales';
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

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        try {
            $lastId    = 0;
            $saleModel = new Sale();
            $sales     = $saleModel->whereHas('project', function($query) {
                $query->whereHas('shopifyIntegrations');
            })->with('project.shopifyIntegrations')->whereIn('status', [1, 2])
                                   ->where('created_at', '>', '2019-11-25')->whereNull('shopify_order')->limit(100)
                                   ->get();

            foreach ($sales as $sale) {
                $lastId             = $sale->id;
                $shopifyIntegration = ShopifyIntegration::where('project_id', $sale->project_id)->first();
                /** @var ShopifyService $shopifyService */
                $shopifyService = new ShopifyService($shopifyIntegration);
                $shopifyService->newOrder($sale);
            }
        } catch (Exception $e) {
            Log::emergency('erro ao criar uma ordem pendente no shopify com a venda ' . $lastId);
            report($e);
        }
    }
}
