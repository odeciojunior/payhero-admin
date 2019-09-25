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
            $subTotal += preg_replace("/[^0-9]/", "", $planSale->plan_value) * $planSale->amount;
        }

        return $subTotal;
    }

    public function getProducts()
    {
        $productsSale = [];
        foreach ($this->plansSales as $planSale) {
            foreach ($planSale->plan()->first()->productPlans as $productPlan) {
                $product           = $productPlan->product()->first()->toArray();
                $product['amount'] = $productPlan->amount * $planSale->amount;
                $productsSale[]    = $product;
            }
        }

        return $productsSale;
    }

    public function getHotzappPlansList()
    {
        $plans = [];

        foreach ($this->plansSales as $planSale) {
            $plans[] = [
                "price"        => $planSale->plan()->first()->price,
                "quantity"     => $planSale->amount,
                "product_name" => $planSale->plan()->first()->name,
            ];
        }

        return $plans;
    }

    public function getStatus($status)
    {

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'approved';
                case 2:
                    return 'pending';
                case 3:
                    return 'refused';
                case 4:
                    return 'charge_back';
                case 5:
                    return 'canceled';
                case 6:
                    return 'in_proccess';
                case 10:
                    return 'system_error';
            }

            return '';
        } else {
            switch ($status) {
                case 'approved':
                    return 1;
                case 'pending':
                    return 2;
                case 'refused':
                    return 3;
                case 'charge_back':
                    return 4;
                case 'canceled':
                    return 5;
                case 'in_proccess':
                    return 6;
                case 'system_error':
                    return 10;
            }

            return '';
        }
    }
}
