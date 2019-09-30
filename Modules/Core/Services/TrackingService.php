<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;

class TrackingService
{
    /**,
     * @param Sale $sale
     * @return array
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function getTrackingProducts(Sale $sale)
    {
        $productsSale         = [];
        $productPlanSaleModel = new ProductPlanSale();
        /** @var PlanSale $planSale */
        foreach ($sale->plansSales as $planSale) {
            /** @var ProductPlan $productPlan */
            foreach ($planSale->plan->productsPlans as $productPlan) {
                $productPlanSale                 = $productPlan->product()
                                                               ->first()->productPlanSales->where('sale_id', $sale->id)
                                                                                          ->first();
                $product                         = $productPlan->product()->first()->toArray();
                $product['amount']               = $productPlan->amount * $planSale->amount;
                $product['tracking_code']        = $productPlanSale->tracking_code ?? '';
                $product['tracking_status_enum'] = $productPlanSale->tracking_status_enum != null ?
                    Lang::get('definitions.enum.product_plan_sale.tracking_status_enum.' . $productPlanSaleModel->present()
                                                                                                                ->getStatusEnum($productPlanSale->tracking_status_enum)) : 'Não informado';
                $productsSale[]                  = $product;
            }
        }

        return $productsSale;
    }
}
