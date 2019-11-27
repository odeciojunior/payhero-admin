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

    public function getTrackingsQueryBuilder($filters)
    {
        $trackingModel = new Tracking();
        $productPlanSaleModel = new ProductPlanSale();

        $productPlanSales = $productPlanSaleModel
            ->with([
                'tracking',
                'sale.plansSales.plan.productsPlans',
                'sale.delivery',
                'sale.client',
                'product',
            ])
            ->whereHas('sale', function ($query) use ($filters) {
                //tipo da data e periodo obrigatorio
                $dateRange = FoxUtils::validateDateRange($filters["date_updated"]);
                $query->whereBetween('end_date', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'])
                    ->where('status', 1)
                    ->where('owner_id', auth()->user()->account_owner_id);

                if(isset($filters['sale'])){
                    $saleId =  current(Hashids::connection('sale_id')->decode($filters['sale']));
                    $query->where('id', $saleId);
                }
            });

        if (isset($filters['status'])) {
            if ($filters['status'] === 'unknown') {
                $productPlanSales->doesntHave('tracking');
            } else {
                $productPlanSales->whereHas('tracking', function ($query) use ($trackingModel, $filters) {
                    $query->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum($filters['status']));
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

        return $productPlanSales;
    }

    public function getPaginatedTrackings($filters)
    {
        $productPlanSales = $this->getTrackingsQueryBuilder($filters);

        return $productPlanSales->orderBy('id', 'desc')->paginate(10);
    }

    public function getAllTrackings($filters)
    {
        $productPlanSales = $this->getTrackingsQueryBuilder($filters);

        return $productPlanSales->get();
    }
}
