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
            ->leftJoin('trackings as t', function ($join) {
                $join->on('pps.id', '=', 't.product_plan_sale_id')
                    ->whereIn('t.system_status_enum', [Tracking::SYSTEM_STATUS_VALID, Tracking::SYSTEM_STATUS_CHECKED_MANUALLY]);
            })
            ->where(function ($query) {
                $query->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('products as p')
                        ->where('type_enum', Product::TYPE_PHYSICAL)
                        ->whereColumn('p.id', 'pps.product_id');
                })->orWhereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('products_sales_api as psa')
                        ->where('product_type', 'physical_goods')
                        ->whereColumn('psa.id', 'pps.products_sales_api_id');
                });
            })
            ->where('sales.id', $event->saleId)
            ->where('sales.status', Sale::STATUS_APPROVED)
            ->groupBy('sales.id')
            ->having(DB::raw('count(pps.id)'), '=', DB::raw('count(t.id)'))
            ->first();


        if (!empty($sale)) {
            $sale->has_valid_tracking = true;
            $sale->save();
        }
    }
}
