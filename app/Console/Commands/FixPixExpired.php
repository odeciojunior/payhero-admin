<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\ShopifyService;

class FixPixExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FixPixExpired';

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
        $sales = Sale::where('payment_method', Sale::PIX_PAYMENT)
            ->where('status', Sale::STATUS_CANCELED)
            ->get();

        foreach ($sales as $sale) {
            foreach ($sale->transactions as $transaction) {
                $transaction->update(
                    [
                        'status' => 'canceled',
                        'status_enum' => Transaction::STATUS_CANCELED,
                    ]
                );
            }

            SaleLog::create(
                [
                    'status' => 'canceled',
                    'status_enum' => 5,
                    'sale_id' => $sale->id,
                ]
            );

            if (!empty($sale->shopify_order)) {
                try {
                    $shopifyIntegration = $sale->project->shopifyIntegrations->first();
                    if (!empty($shopifyIntegration)) {
                        $shopifyService = new ShopifyService(
                            $shopifyIntegration->url_store,
                            $shopifyIntegration->token
                        );

                        $shopifyService->cancelOrder($sale);
                    }
                } catch (Exception $e) {
                    report($e);
                }
            }
        }
    }
}
