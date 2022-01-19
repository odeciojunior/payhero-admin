<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\ProductSaleApi;

class FillProductsPlansSalesFromApiSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-sales-fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        foreach(ProductSaleApi::all() as $productSaleApi) {
            ProductPlanSale::create(
                [
                    'products_sales_api_id' => $productSaleApi->id,
                    'sale_id' => $productSaleApi->sale_id,
                    'amount' => $productSaleApi->quantity,
                    'name' => $productSaleApi->name,
                ]
            );
        }
    }
}
