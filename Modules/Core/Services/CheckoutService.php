<?php

namespace Modules\Core\Services;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Domain;

/**
 * Class CheckoutService
 * @package Modules\Core\Services
 */
class CheckoutService
{
    /**
     * @param string|null $projectId
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @param string|null $client
     * @return AnonymousResourceCollection
     */
    public function getAbandonedCart(string $projectId = null, string $dateStart = null, string $dateEnd = null, string $client = null)
    {
        $checkoutModel = new Checkout();
        $domainModel   = new Domain();

        $abandonedCarts = $checkoutModel->whereIn('status', ['recovered', 'abandoned cart'])
                                        ->where('project_id', $projectId);

        if (!empty($client)) {
            $abandonedCarts->where('client_name', 'like', '%' . $client . '%');
        }

        if (!empty($dateStart) && !empty($dateEnd)) {
            $abandonedCarts->whereBetween('checkouts.created_at', [$dateStart, $dateEnd]);
        } else {
            if (!empty($dateStart)) {
                $abandonedCarts->whereDate('checkouts.created_at', '>=', $dateStart);
            }
            if (!empty($dateEnd)) {
                $abandonedCarts->whereDate('checkouts.created_at', '<', $dateEnd);
            }
        }

        return $abandonedCarts->with([
                                         'project.domains' => function($query) use ($domainModel) {
                                             $query->where('status', $domainModel->present()->getStatus('approved'));
                                         },
                                         'checkoutPlans.plan',
                                     ])->orderBy('id', 'DESC')->paginate(10);
    }

    /**
     * @param null $checkoutPlans
     * @return float|int
     */
    public function getSubTotal($checkoutPlans = null)
    {
        $total = 0;
        foreach ($checkoutPlans as $checkoutPlan) {
            $total += intval(preg_replace("/[^0-9]/", "", $checkoutPlan->plan->price)) * intval($checkoutPlan->amount);
        }

        return $total;
    }
    /*
        public function getProducts()
        {
            $products = [];
            foreach ($this->checkoutPlans as $checkoutPlan) {
                foreach ($checkoutPlan->plan()->first()->productsPlans as $productPlan) {
                    $product           = $productPlan->product()->first()->toArray();
                    $product['amount'] = $productPlan->amount * $checkoutPlan->amount;
                    $products[]        = $product;
                }
            }

            return $products;
        }*/
}
