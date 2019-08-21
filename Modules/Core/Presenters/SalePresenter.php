<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class SalePresenter extends Presenter
{
    public function getTotalPaidValue()
    {
        return number_format($this->total_paid_value, 2, ',', '.');
    }

    public function getShipmentValue()
    {
        return number_format($this->shipment_value, 2, ',', '.');
    }

    public function getInstallmentsValue()
    {
        return number_format($this->installments_value, 2, ',', '.');
    }

    public function getIofValue()
    {
        return number_format(intval($this->iof) / 100, 2, ',', '.');
    }

    public function getShopifyDiscount()
    {
        return ($this->shopify_discount != '' && $this->shopify_discount != '0') ? number_format(preg_replace("/[^0-9]/", "", $this->shopify_discount) / 100, 2, ',', '.') : '0,00';
    }

    public function getBoletoDueDate()
    {
        return date('d/m/Y', strtotime($this->boleto_due_date));
    }

    public function getSubTotal()
    {
        $subTotal = 0;
        foreach ($this->plansSales as $planSale) {
            $subTotal += preg_replace("/[^0-9]/", "", $planSale->plan()->first()->price) * $planSale->amount;
        }
        return $subTotal;
    }

    public function getProducts()
    {
        $productsSale = [];
        foreach ($this->plansSales as $planSale) {
            foreach ($planSale->plan()->first()->productsPlans as $productPlan) {
                $product = $productPlan->getProduct()->first()->toArray();
                $product['amount'] = $productPlan->amount * $planSale->amount;
                $productsSale[]    = $product;
            }
        }
        return $productsSale;
    }
}
