<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;

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
        $projectModel = new Project();
        $project = $projectModel->find($projectId);
        if (!empty($projectId) && !empty($project->shopify_id)) {
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
