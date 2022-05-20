<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\CustomerCard;

class CustomerCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerCard::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $creditCard = $this->faker->creditCardNumber();
        $firstSixDigits = substr($creditCard, 0, 6);
        $lastFourDigits = substr($creditCard, -4);
        
        return [
            'customer_id' => Customer::factory(),
            'first_six_digits' => $firstSixDigits,
            'last_four_digits' => $lastFourDigits,
            'card_token' => $this->faker->sha256(),
            'association_code' => '',
        ];
    }    
}
