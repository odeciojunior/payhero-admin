<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;

class TrackingService
{
    /**,
     * @param Sale $sale
     * @return array
     */
    public function getTrackingProducts(Sale $sale)
    {
        $productsSale = [];
        /** @var PlanSale $planSale */
        foreach ($sale->plansSales as $planSale) {
            /** @var ProductPlan $productPlan */
            foreach ($planSale->plan->productsPlans as $productPlan) {
                $productPlanSale = $productPlan->product->first()->productPlanSales->where('sale_id', $sale->id)->first();
                $product                         = $productPlan->product()->first()->toArray();
                $product['amount']               = $productPlan->amount * $planSale->amount;
                $product['tracking_code']        = $productPlanSale->tracking_code ?? '';
                $product['tracking_status_enum'] = $productPlanSale->tracking_status_enum ?? 'Não informado';
                $productsSale[]                  = $product;
            }
        }

        return $productsSale;
    }
}
