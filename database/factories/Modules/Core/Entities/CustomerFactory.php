<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Customer;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->name(),
            'document'=>$this->faker->randomNumber(11),
            'email'=>$this->faker->email(),
            'telephone'=>$this->faker->e164PhoneNumber(),
            'balance'=>0,
            'blocked_withdrawal'=>0,
            'birthday'=>$this->faker->date('Y-m-d','-'.rand(18,70).' years'),
            'id_kapsula_client'=>null,
        ];
    }    
}
