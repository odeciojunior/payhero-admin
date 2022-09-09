<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\FoxUtilsService;
use Vinkla\Hashids\Facades\Hashids;

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
        return number_format($this->total_paid_value, 2, ",", ".");
    }

    /**
     * @return string
     */
    public function getShipmentValue()
    {
        return number_format($this->shipment_value, 2, ",", ".");
    }

    public function getInstallmentValue()
    {
        return number_format(intval($this->installment_tax_value) / 100, 2, ",", ".");
    }

    /**
     * @return string
     */
    public function getInstallmentsValue()
    {
        return number_format($this->installments_value, 2, ",", ".");
    }

    /**
     * @return array
     */
    public function getHotzappPlansList()
    {
        $plans = [];

        foreach ($this->plansSales as $planSale) {
            $plans[] = [
                "price" => $planSale->plan()->first()->price,
                "quantity" => $planSale->amount,
                "product_name" => $planSale->plan()->first()->name . " - " . $planSale->plan()->first()->description,
            ];
        }

        return $plans;
    }

    /**
     * @return array
     */
    public function getHotBilletPlansList()
    {
        $plans = [];
        foreach ($this->plansSales as $planSale) {
            $plans[] = [
                "price" => $planSale->plan()->first()->price,
                "quantity" => $planSale->amount,
                "product_name" => $planSale->plan()->first()->name . " - " . $planSale->plan()->first()->description,
                "id" => hashids_encode($planSale->plan()->first()->id),
            ];
        }
        return $plans;
    }

    /**
     * @return string
     */
    public function getShopifyDiscount()
    {
        return $this->shopify_discount != "" && $this->shopify_discount != "0"
            ? number_format(preg_replace("/[^0-9]/", "", $this->shopify_discount) / 100, 2, ",", ".")
            : "0,00";
    }

    /**
     * @return string|string[]
     */
    public function getFormattedShipmentValue()
    {
        if (!is_null($this->delivery_id)) {
            $shipmentValeu = preg_replace("/[^0-9]/", "", $this->shipment_value);

            return substr_replace($shipmentValeu, ",", strlen($shipmentValeu) - 2, 0);
        } else {
            return "";
        }
    }

    /**
     * @return false|string
     */
    public function getBoletoDueDate()
    {
        return date("d/m/Y", strtotime($this->boleto_due_date));
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
                    return "approved";
                case 2:
                    return "pending";
                case 3:
                    return "refused";
                case 4:
                    return "charge_back";
                case 5:
                    return "canceled";
                case 6:
                    return "in_proccess";
                case 7:
                    return "refunded";
                case 8:
                    return "partial_refunded";
                case 10:
                    return "black_list";
                case 20:
                    return "in_review";
                case 21:
                    return "canceled_antifraud";
                case 22:
                    return "billet_refunded";
                case 24:
                    return "in_dispute";
                case 99:
                    return "system_error";
            }

            return "";
        } else {
            switch ($status) {
                case "paid":
                case "approved":
                    return 1;
                case "pending":
                    return 2;
                case "refused":
                    return 3;
                case "charge_back":
                    return 4;
                case "canceled":
                    return 5;
                case "in_proccess":
                    return 6;
                case "refunded":
                    return 7;
                case "partial_refunded":
                    return 8;
                case "black_list":
                    return 10;
                case "in_review":
                    return 20;
                case "canceled_antifraud":
                    return 21;
                case "billet_refunded":
                    return 22;
                case "in_dispute":
                    return 24;
                case "system_error":
                    return 99;
            }

            return "";
        }
    }

    /**
     * @return float|int
     */
    public function getSubTotal()
    {
        return preg_replace("/[^0-9]/", "", $this->sub_total);
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        $productsSale = [];
        foreach ($this->plansSales as $planSale) {
            foreach ($planSale->plan()->first()->productsPlans as $productPlan) {
                $product = $productPlan
                    ->product()
                    ->first()
                    ->toArray();
                if (
                    is_object(
                        $productPlan
                            ->product()
                            ->first()
                            ->toArray()
                    )
                ) {
                    $product = clone $productPlan
                        ->product()
                        ->first()
                        ->toArray();
                }
                $product["amount"] = $productPlan->amount * $planSale->amount;
                $productsSale[] = $product;
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
        $paymentType = $paymentType ?? $this->payment_method;

        if (is_numeric($paymentType)) {
            switch ($paymentType) {
                case 1:
                    return "credit_card";
                case 2:
                    return "boleto";
                case 3:
                    return "debito";
                case 4:
                    return "pix";
            }

            return null;
        } else {
            switch ($paymentType) {
                case "credit_card":
                    return 1;
                case "boleto":
                    return 2;
                case "debito":
                    return 3;
                case "pix":
                    return 4;
            }

            return null;
        }
    }

    /**
     * @return array
     */
    public function getProductsData()
    {
        $productsSale = [];
        foreach ($this->plansSales as $planSale) {
            foreach ($planSale->plan()->first()->productsPlans as $productPlan) {
                $saleProduct = $productPlan->product()->first();

                $product = [];
                $product["id"] = Hashids::encode($saleProduct->id);
                $product["name"] = $saleProduct->name;
                $product["description"] = $saleProduct->description;
                $product["amount"] = $productPlan->amount * $planSale->amount;
                $product["photo"] = $saleProduct->photo;
                $product["created_at"] = $saleProduct->created_at->format("d/m/Y H:i:s");
                $productsSale[] = $product;
            }
        }

        return $productsSale;
    }

    /**
     * @return array
     */
    public function getProductsApiData()
    {
        $productsApi = [];
        foreach ($this->productsSaleApi as $productSale) {
            $product = [];
            $product["id"] = $productSale->item_id;
            $product["name"] = $productSale->name;
            $product["price"] = $productSale->price;
            $product["quantity"] = $productSale->quantity;
            $product["product_type"] = $productSale->product_type;

            $productsApi[] = $product;
        }

        return $productsApi;
    }

    /**
     * @param null $paymentFlag
     * @return int|string|null
     */
    public function getPaymentForm($paymentType = null)
    {
        $paymentType = $paymentType ?? $this->payment_method;

        if (is_numeric($paymentType)) {
            switch ($paymentType) {
                case 1:
                case 3:
                    return "Cartão";
                case 2:
                    return "Boleto";
                case 4:
                    return "Pix";
            }
        }
        return null;
    }

    /**
     * @param null $paymentFlag
     * @return int|string|null
     */
    public function getPaymentFlag($paymentType = null)
    {
        $paymentType = $paymentType ?? $this->payment_method;

        if (is_numeric($paymentType)) {
            switch ($paymentType) {
                case 1:
                    return "generico";
                case 3:
                    return "debito";
                case 2:
                    return "boleto";
                case 4:
                    return "pix";
            }
        }
        return null;
    }

    public function getFormattedSubTotal()
    {
        return substr_replace($this->getSubTotal(), ",", strlen($this->getSubTotal()) - 2, 0);
    }

    public function getFormattedDiscount()
    {
        $discount = preg_replace("/[^0-9]/", "", $this->shopify_discount);

        if (empty($discount)) {
            return "";
        } else {
            return substr_replace($discount, ",", strlen($discount) - 2, 0);
        }
    }

    public function getTotalPaidValueWithoutInstallmentTax()
    {
        $val = foxutils()->onlyNumbers($this->sub_total) + foxutils()->onlyNumbers($this->shipment_value);
        if (!empty($this->shopify_discount)) {
            $val -= foxutils()->onlyNumbers($this->shopify_discount);
        }
        return substr_replace($val, ",", strlen($val) - 2, 0);
    }
}
