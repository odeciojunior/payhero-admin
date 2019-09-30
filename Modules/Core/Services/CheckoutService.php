<?php

namespace Modules\Core\Services;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;

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

        $abandonedCarts = $checkoutModel->select('checkouts.id', 'checkouts.created_at', 'checkouts.project_id', 'checkouts.id_log_session', 'checkouts.status', 'checkouts.email_sent_amount', 'checkouts.sms_sent_amount', 'logs.name', 'logs.telephone')
                                        ->leftjoin('logs', function($join) {
                                            $join->on('logs.id', '=', DB::raw("(select max(logs.id) from logs WHERE logs.id_log_session = checkouts.id_log_session)"));
                                        })
                                        ->whereIn('status', ['recovered', 'abandoned cart']);

        $abandonedCarts->where('project_id', $projectId);

        if (!empty($client)) {
            $abandonedCarts->where('name', 'like', '%' . $client . '%');
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

        $abandonedCarts = $abandonedCarts->with([
                                                    'project.domains' => function($query) {
                                                        $query->where('status', 3);
                                                    },
                                                    'checkoutPlans.plan',
                                                ])->orderBy('id', 'DESC')->simplePaginate(10);

        return $abandonedCarts;
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
