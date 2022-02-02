<?php


namespace Modules\Core\Listeners;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Events\CheckSaleHasValidTrackingEvent;

class CheckSaleHasValidTrackingListener implements ShouldQueue
{
    use Queueable;

    /**
     * @param CheckSaleHasValidTrackingEvent $event
     */
    public function handle(CheckSaleHasValidTrackingEvent $event)
    {
        $sale = Sale::select('sales.id', 'sales.has_valid_tracking')
            ->join('products_plans_sales as pps', 'sales.id', '=', 'pps.sale_id')
            ->leftJoin('products as p', function ($join) {
                $join->on('pps.product_id', '=', 'p.id')
                    ->where('p.type_enum', Product::TYPE_PHYSICAL);
            })
            ->leftJoin('products_sales_api as psa', function ($join) {
                $join->on('pps.products_sales_api_id', '=', 'psa.id')
                    ->where('psa.product_type', 'physical_goods');
            })
            ->leftJoin('trackings as t', function ($join) {
                $join->on('pps.id', '=', 't.product_plan_sale_id')
                    ->whereIn('t.system_status_enum', [Tracking::SYSTEM_STATUS_VALID, Tracking::SYSTEM_STATUS_CHECKED_MANUALLY]);
            })
            ->where('sales.id', $event->saleId)
            ->groupBy('sales.id')
            ->having(DB::raw('count(pps.id)'), '=', DB::raw('count(t.id)'))
            ->first();

        if (!empty($sale)) {
            $sale->has_valid_tracking = true;
            $sale->save();
        }
    }
}
