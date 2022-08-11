<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\ShopifyService;

class ShopifyReorderSalesHourly extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = "command:ShopifyReorderSalesHourly";
    /**
     * The console command description.
     * @var string
     */
    protected $description = "Command description";

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
            // 1:30 atrás até 0:30
            $saleModel = new Sale();
            $salePresenter = $saleModel->present();
            $sales = $saleModel
                ->whereNull("shopify_order")
                ->whereIn("status", [$salePresenter->getStatus("approved"), $salePresenter->getStatus("pending")])
                ->whereBetween("created_at", [
                    Carbon::now()
                        ->subHour()
                        ->subMinutes(30)
                        ->toDateTimeString(),
                    Carbon::now()
                        ->subMinutes(30)
                        ->toDateTimeString(),
                ])
                ->whereHas("project.shopifyIntegrations", function ($query) {
                    $query->where("status", 2);
                })
                ->get();

            foreach ($sales as $sale) {
                try {
                    $shopifyIntegration = ShopifyIntegration::where("project_id", $sale->project_id)->first();

                    $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                    $shopifyService->newOrder($sale);

                    $this->line("sucesso");
                } catch (Exception $e) {
                    $this->line("erro -> " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
