<?php

namespace Database\Factories\Modules\Core\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;

class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "progressive_discount" => null,
            "owner_id" => User::DEMO_ID,
            "gateway_id" => Gateway::SAFE2PAY_PRODUCTION_ID,
            "customer_id" => null,
            "project_id" => null,
            "shipping_id" => null,
            "checkout_id" => null,
            "affiliate_id" => null,
            "payment_method" => null,
            "total_paid_value" => null,
            "original_total_paid_value" => null,
            "sub_total" => null,
            "shipment_value" => null,
            "cupom_code" => "",
            "start_date" => Carbon::now(),
            "gateway_transaction_id" => "",
            "gateway_status" => "",
            "installments_amount" => null,
            "installments_value" => null,
            "flag" => null,
            "delivery_id" => null,
            "shopify_discount" => "0",
            "installment_tax_value" => null,
            "upsell_id" => null,
            "automatic_discount" => null,
            "interest_total_value" => 0,
            "has_order_bump" => 0,
            "status" => Sale::STATUS_PENDING,
            "gateway_status" => function (array $attributes) {
                return $this->getSaleStatus($attributes["status"]);
            },
            "boleto_digitable_line" => null,
            "gateway_billet_identificator" => null,
            "boleto_due_date" => null,
            "has_valid_tracking" => $this->faker->boolean(),
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }

    public function getSaleStatus($status)
    {
        switch ($status) {
            case Sale::STATUS_PENDING:
                return "pending";
            case Sale::STATUS_APPROVED:
                return "paid";
            case Sale::STATUS_REFUSED:
                return "refused";
            case Sale::STATUS_IN_REVIEW:
                return "in_review";
            case Sale::STATUS_CANCELED_ANTIFRAUD:
                return "canceled_antifraud";
            case Sale::STATUS_CANCELED:
                return "canceled";
        }
    }

    public function getTransactionStatus($status)
    {
        switch ($status) {
            case Sale::STATUS_PENDING:
                return "PENDING";
            case Sale::STATUS_APPROVED:
                return "CONFIRMED";
            case Sale::STATUS_REFUSED:
                return "DENIED";
            case Sale::STATUS_IN_REVIEW:
                return "IN_REVIEW";
            case Sale::STATUS_CANCELED_ANTIFRAUD:
            case Sale::STATUS_CANCELED:
                return "CANCELED";
        }
    }
}
