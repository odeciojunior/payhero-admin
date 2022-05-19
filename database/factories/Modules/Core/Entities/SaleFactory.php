<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Sale;

class CustomerFactory extends Factory
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
            'owner_id'=>Company::USER_ID_DEMO,
            'affiliate_id'=>null,
            'customer_id'=>Customer::factory(),
            'delivery_id',
            'shipping_id',
            'project_id',
            'checkout_id',
            'payment_form',
            'payment_method',
            'total_paid_value',
            'sub_total',
            'shipment_value',
            'start_date',
            'date_refunded',
            'end_date',
            'gateway_transaction_id',
            'gateway_order_id',
            'gateway_billet_identificator',
            'gateway_id',
            'status',
            'gateway_status',
            'upsell_id',
            'installments_amount',
            'installments_value',
            'flag',
            'boleto_link',
            'boleto_digitable_line',
            'boleto_due_date',
            'cupom_code',
            'shopify_order',
            'woocommerce_order',
            'shopify_discount',
            'dolar_quotation',
            'first_confirmation',
            'api_flag',
            'installment_tax_value',
            'attempts',
            'created_at',
            'deleted_at',
            'updated_at',
            'gateway_card_flag',
            'gateway_tax_percent',
            'gateway_tax_value',
            'automatic_discount',
            'interest_total_value',
            'refund_value',
            'is_chargeback',
            'is_chargeback_recovered',
            'has_valid_tracking',
            'has_order_bump',
            'observation',
            'original_total_paid_value',
            'antifraud_warning_level'
        ];
    }    
}
