<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\ShopifyService;

class ShopifyReorderSales extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'command:ShopifyReorderSales';
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

        try {

            //Dia anterior
            $saleModel     = new Sale();
            $salePresenter = $saleModel->present();
            $date          = Carbon::now()->subDay()->toDateString();
            $sales         = $saleModel->whereNull('shopify_order')
                ->whereIn('status',
                          [
                              $salePresenter->getStatus('approved'),
                              $salePresenter->getStatus('pending'),
                          ])
                ->whereDate('created_at', $date)
                ->whereHas('project.shopifyIntegrations', function($query) {
                    $query->where('status', 2);
                })
                ->get();

            foreach ($sales as $sale) {
                try {
                    $shopifyIntegration = ShopifyIntegration::where('project_id', $sale->project_id)->first();

                    $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                    $shopifyService->newOrder($sale);

                    $this->line('sucesso');
                } catch (Exception $e) {
                    $this->line('erro -> ' . $e->getMessage());
                }
            }

        } catch (Exception $e) {
            report($e);
        }

    }
}
