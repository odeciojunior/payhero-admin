<?php

namespace Database\Factories\Modules\Core\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;

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
            'progressive_discount' => null,
            'owner_id' => Company::USER_ID_DEMO,
            'customer_id' =>  null,
            'project_id' =>  null,
            'shipping_id' =>  null,
            'checkout_id' =>  null,
            'affiliate_id' =>  null,
            'payment_method' =>  null,
            'total_paid_value' =>  null,
            'original_total_paid_value' =>  null,
            'sub_total' =>  null,
            'shipment_value' =>  null,
            'cupom_code' => '',
            'start_date' => Carbon::now(),
            'gateway_transaction_id' => '',
            'gateway_status' => '',
            'installments_amount' => null,
            'installments_value' => null,
            'flag' => function (array $attributes) {
                return $attributes['payment_method'] == Sale::CREDIT_CARD_PAYMENT ?  $this->getRandoFlagCC(): '';
            },
            'delivery_id' => null,
            'shopify_discount' => '0',
            'installment_tax_value' => null,
            'upsell_id' => null,
            'automatic_discount' => null,
            'interest_total_value' => 0,
            'has_order_bump' => 0,
            'status'=>Sale::STATUS_PENDING,
            'gateway_status'=>function (array $attributes) {
                return $this->getSaleStatus($attributes['status']);
            }
        ];
    }   

    public function getRandoFlagCC(){
        $flags = ['visa','mastercad','aura','discover','hipercard','amex','elo','diners','jcb'];
        return Arr::random($flags);
    }
    
    public function getRandomStatus($paymentMethod){
        $status = [Sale::STATUS_PENDING, Sale::STATUS_APPROVED,Sale::STATUS_CANCELED];
        if($paymentMethod == Sale::CREDIT_CARD_PAYMENT){
            $status = [Sale::STATUS_APPROVED,Sale::STATUS_CANCELED_ANTIFRAUD, Sale::STATUS_IN_REVIEW,sale::STATUS_REFUSED];
        }
        return Arr::random($status);
    }

    public function getSaleStatus($status)
    {        
        switch ($status) {
            case Sale::STATUS_PENDING:
                return 'pending';                    
            case Sale::STATUS_APPROVED:
                return 'paid';                    
            case Sale::STATUS_REFUSED:
                return 'refused';     
            case Sale::STATUS_IN_REVIEW:
                return 'in_review'; 
            case Sale::STATUS_CANCELED_ANTIFRAUD:
                return 'canceled_antifraud';               
            case Sale::STATUS_CANCELED:
                return 'canceled';
        }       
    }

    public function getTransactionStatus($status){
        switch ($status) {
            case Sale::STATUS_PENDING:
                return 'PENDING';                    
            case Sale::STATUS_APPROVED:
                return 'CONFIRMED';                    
            case Sale::STATUS_REFUSED:
                return 'DENIED';     
            case Sale::STATUS_IN_REVIEW:
                return 'IN_REVIEW'; 
            case Sale::STATUS_CANCELED_ANTIFRAUD:
                case Sale::STATUS_CANCELED:
                return 'CANCELED';                               
        }       

    }
}
