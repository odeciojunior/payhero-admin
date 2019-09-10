<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Product;

class ProductService
{
    /**
     * @param int $projectId
     * @return mixed
     * Retorna produtos
     */
    public function getProductsMyProject(int $projectId)
    {
        $productModel = new Product();

        if (!empty($projectId)) {
            return $productModel->where('user_id', auth()->user()->id)
                                ->where('shopify', 1)
                                ->whereHas('productsPlans.plan', function($queryPlan) use ($projectId) {
                                    $queryPlan->where('project_id', $projectId);
                                })->get();
        } else {
            return $productModel->where('user_id', auth()->user()->id)
                                ->where('shopify', 0)->get();
        }
    }
}
