<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\CheckoutPlan;

class CheckoutPlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CheckoutPlan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'checkout_id'=>Checkout::factory(),
            'plan_id'=>null,
            'amount'=>1,
            'created_at'=>now(),
            'updated_at'=>now()
        ];
    }    
}
