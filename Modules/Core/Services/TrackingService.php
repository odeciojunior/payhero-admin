<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Company;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Tracking;
use Vinkla\Hashids\Facades\Hashids;

class TrackingService
{

    public function createTracking(string $trackingCode, ProductPlanSale $productPlanSale)
    {
        $trackingModel = new Tracking();

        $planSale = $productPlanSale
            ->sale
            ->plansSales
            ->where('plan_id', $productPlanSale->plan_id)
            ->where('sale_id', $productPlanSale->sale_id)
            ->first();

        $productPlan = $planSale->plan
            ->productsPlans
            ->where('product_id', $productPlanSale->product_id)
            ->where('plan_id', $productPlanSale->plan_id)
            ->first();

        $amount = $productPlan->amount * $planSale->amount;

        $tracking = $trackingModel->create([
            'sale_id' => $productPlanSale->sale->id,
            'product_id' => $productPlanSale->product_id,
            'product_plan_sale_id' => $productPlanSale->id,
            'plans_sale_id' => $planSale->id,
            'amount' => $amount,
            'delivery_id' => $productPlanSale->sale->delivery->id,
            'tracking_code' => $trackingCode,
            'tracking_status_enum' => $trackingModel->present()
                ->getTrackingStatusEnum('posted'),
        ]);

        return $tracking;
    }

    public function getTrackings($filters, $resume = false)
    {
        $trackingModel = new Tracking();
        $productPlanSaleModel = new ProductPlanSale();
        $companyModel = new Company();

        $userCompanies = $companyModel->where('user_id', auth()->user()->id)
            ->pluck('id')
            ->toArray();

        $productPlanSales = $productPlanSaleModel
            ->with([
                'tracking',
                'sale',
                'product',
            ])
            ->whereHas('sale', function ($query) use ($userCompanies) {
                $query->where('status', 1);
            })
            ->whereHas('sale.transactions', function ($query) use ($userCompanies) {
                $query->whereIn('company_id', $userCompanies);
            });

        if (isset($filters['status'])) {
            if ($filters['status'] === 'unknown') {
                $productPlanSales->doesntHave('tracking');
            } else {
                $productPlanSales->whereHas('tracking', function ($query) use ($trackingModel, $filters) {
                    $query->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum($filters['status']));
                });
                //tipo da data e periodo obrigatorio
                $dateRange = FoxUtils::validateDateRange($filters["date_updated"]);
                $productPlanSales->doesntHave('tracking')->orWhereHas('trackings', function ($query) use ($dateRange) {
                    $query->whereBetween('updated_at', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
                });
            }
        }

        if (isset($filters['tracking_code'])) {
            $productPlanSales->whereHas('tracking', function ($query) use ($filters) {
                $query->where('tracking_code', 'like', '%' . $filters['tracking_code'] . '%');
            });
        }

        if (isset($filters['project'])) {
            $productPlanSales->whereHas('product', function ($query) use ($filters) {
                $query->where('project_id', current(Hashids::decode($filters['project'])));
            });
        }

        if (!$resume) {
            return $productPlanSales->orderBy('id', 'desc')->paginate(10);
        } else {

            $productPlanSales = $productPlanSales->get();

            $total = $productPlanSales->count();
            $posted = 0;
            $dispatched = 0;
            $delivered = 0;
            $out_for_delivery = 0;
            $exception = 0;
            $unknown = 0;

            foreach ($productPlanSales as $productPlanSale) {

                $tracking = $productPlanSale->tracking;

                if (isset($tracking)) {
                    switch ($tracking->tracking_status_enum) {
                        case $tracking->present()->getTrackingStatusEnum('posted'):
                            $posted++;
                            break;
                        case $tracking->present()->getTrackingStatusEnum('dispatched'):
                            $dispatched++;
                            break;
                        case $tracking->present()->getTrackingStatusEnum('delivered'):
                            $delivered++;
                            break;
                        case $tracking->present()->getTrackingStatusEnum('out_for_delivery'):
                            $out_for_delivery++;
                            break;
                        case $tracking->present()->getTrackingStatusEnum('exception'):
                            $exception++;
                            break;
                    }
                } else {
                    $unknown++;
                }
            }

            return response()->json(['data' => [
                'total' => $total,
                'posted' => $posted,
                'dispatched' => $dispatched,
                'delivered' => $delivered,
                'out_for_delivery' => $out_for_delivery,
                'exception' => $exception,
                'unknown' => $unknown
            ]]);
        }
    }
}
