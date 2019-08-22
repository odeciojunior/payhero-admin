<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class CheckoutPresenter extends Presenter
{
    public function getTotal()
    {
        $total = 0;
        foreach ($this->checkoutPlans as $checkoutPlan) {
            $total += intval(preg_replace("/[^0-9]/", "", $checkoutPlan->plan()
                                                                       ->first()->price)) * intval($checkoutPlan->amount);
        }

        return $total;
    }

    public function getProducts()
    {
        $products = [];
        foreach ($this->checkoutPlans as $checkoutPlan) {
            foreach ($checkoutPlan->plan()->first()->productsPlans as $productPlan) {
                $product           = $productPlan->getProduct()->first()->toArray();
                $product['amount'] = $productPlan->amount * $checkoutPlan->amount;
                $products[]        = $product;
            }
        }
        return $products;
    }
}
