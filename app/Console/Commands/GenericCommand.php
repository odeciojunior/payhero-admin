<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\CheckoutService;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    public function handle()
    {
        try {
            $salesModel = new Sale();
            $trackingPresenter = (new Tracking())->present();
            $productPresenter = (new Product())->present();
            $checkoutService = new CheckoutService();

            $salesQuery = $salesModel::with([
                'productsPlansSale.tracking',
                'productsPlansSale.product',
            ])->whereIn('status', [1, 4])
                ->where('has_valid_tracking', false)
                ->whereHas('productsPlansSale', function ($query) use ($productPresenter) {
                    $query->whereHas('product', function ($query) use ($productPresenter) {
                        $query->where('type_enum', $productPresenter->getType('physical'));
                    });
                })
                ->orderByDesc('id');

            $total = $salesQuery->count();
            $count = 1;

            $salesQuery->chunk(100,
                function ($sales) use ($total, &$count, $checkoutService, $trackingPresenter, $productPresenter) {
                    foreach ($sales as $sale) {
                        $this->line("Verificando venda {$count} de {$total}: {$sale->id}...");
                        try {
                            foreach ($sale->productsPlansSale as $pps) {
                                if ($pps->product->type_enum == $productPresenter->getType('physical')) {
                                    $hasInvalidOrNotInformedTracking = is_null($pps->tracking) || !in_array($pps->tracking->system_status_enum,
                                            [
                                                $trackingPresenter->getSystemStatusEnum('valid'),
                                                $trackingPresenter->getSystemStatusEnum('ignored'),
                                                $trackingPresenter->getSystemStatusEnum('checked_manually'),
                                            ]);
                                    if ($hasInvalidOrNotInformedTracking) {
                                        break;
                                    }
                                }
                            }

                            if (!$hasInvalidOrNotInformedTracking) {
                                $sale->has_valid_tracking = true;
                                $sale->save();
                                $checkoutService->releasePaymentGetnet($sale->id);

                                $this->info("Venda liberada!");
                            } else {
                                $this->line("Venda ainda não liberada!");
                            }
                        } catch (Exception $e) {
                            $this->error('ERROR:'.$e->getMessage());
                        }
                        $count++;
                    }
                });
        } catch (Exception $e) {
            report($e);
        }
    }
}


