<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Delivery;
use Modules\Core\Services\FoxUtilsFakeService;

class DeliveryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Delivery::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_id'=>Customer::factory(),
            'receiver_name'=>function (array $attributes) {
                return Customer::find($attributes['customer_id'])->name;
            },
            'zip_code'=>$this->faker->randomNumber(8),
            'country'=>'BR',
            'state'=>FoxUtilsFakeService::getRandomUf(),
            'city'=>$this->faker->city(),
            'neighborhood'=>$this->faker->city(),
            'street'=>$this->faker->streetAddress(),
            'number'=>$this->faker->randomNumber(5),
            'complement'=>'',
            'type'=>'v2RmA83EbZPVpYB'
        ];
    }    

    
}
