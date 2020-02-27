<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Core\Entities\Sale;

/**
 * Class SalePresenter
 * @property Sale $entity
 * @package Modules\Core\Presenters
 */
class SalePresenter extends Presenter
{
    /**
     * @return string
     */
    public function getTotalPaidValue()
    {
        return number_format($this->total_paid_value, 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getShipmentValue()
    {
        return number_format($this->shipment_value, 2, ',', '.');
    }

    public function getInstallmentValue()
    {
        return number_format(intval($this->installment_tax_value) / 100, 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getInstallmentsValue()
    {
        return number_format($this->installments_value, 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getIofValue()
    {
        return number_format(intval($this->iof) / 100, 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getShopifyDiscount()
    {
        return ($this->shopify_discount != '' && $this->shopify_discount != '0') ? number_format(preg_replace("/[^0-9]/", "", $this->shopify_discount) / 100, 2, ',', '.') : '0,00';
    }

    /**
     * @return false|string
     */
    public function getBoletoDueDate()
    {
        return date('d/m/Y', strtotime($this->boleto_due_date));
    }

    /**
     * @param $status
     * @return int|string
     */
    public function getStatus($status = null)
    {
        $status = $status ?? $this->status;

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
                case 7:
                    return 'refunded';
                case 8:
                    return 'partial_refunded';
                case 10:
                    return 'system_error';
                case 20:
                    return 'in_review';
                case 21:
                    return 'canceled_antifraud';
                case 99:
                    return 'blacklist';
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
                case 'refunded':
                    return 7;
                case 'partial_refunded':
                    return 8;
                case 'system_error':
                    return 10;
                case 'in_review':
                    return 20;
                case 'canceled_antifraud':
                    return 21;
                case 'blacklist':
                    return 99;
            }

            return '';
        }
    }

    /**
     * @return float|int
     */
    public function getSubTotal()
    {
        $subTotal = 0;
        foreach ($this->plansSales as $planSale) {
            $subTotal += preg_replace("/[^0-9]/", "", $planSale->plan()->first()->price) * $planSale->amount;
        }

        return $subTotal;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        $productsSale = [];
        foreach ($this->plansSales as $planSale) {
            foreach ($planSale->plan()->first()->productsPlans as $productPlan) {
                $product           = $productPlan->product()->first()->toArray();
                $product['amount'] = $productPlan->amount * $planSale->amount;
                $productsSale[]    = $product;
            }
        }

        return $productsSale;
    }

    /**
     * @param null $paymentType
     * @return int|string|null
     */
    public function getPaymentType($paymentType = null)
    {
        if (is_numeric($paymentType)) {
            switch ($paymentType) {
                case 1:
                    return 'credit_card';
                case 2:
                    return 'boleto';
                case 3:
                    return 'debito';
            }

            return null;
        } else {
            switch ($paymentType) {
                case 'credit_card':
                    return 1;
                case 'boleto':
                    return 2;
                case 'debito':
                    return 3;
            }

            return null;
        }
    }
}
