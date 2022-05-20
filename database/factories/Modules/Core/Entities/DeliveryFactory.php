<?php

namespace Database\Factories\Modules\Core\Entities;

use Google\Service\Monitoring\Custom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Delivery;

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
            'state'=>$this->getRandomUf(),
            'city'=>$this->faker->city(),
            'neighborhood'=>$this->faker->city(),
            'street'=>$this->faker->streetAddress(),
            'number'=>$this->faker->randomNumber(5),
            'complement'=>'',
            'type'=>'v2RmA83EbZPVpYB'
        ];
    }    

    public function getRandomUf(){
        $ufs = [
            'AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SP','TO'
        ];

        return Arr::random($ufs);
    }
}
