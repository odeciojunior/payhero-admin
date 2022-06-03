<?php

namespace Database\Factories\Modules\Core\Entities;

use Carbon\Carbon;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\PixCharge;
use Modules\Core\Entities\Sale;

class PixChargeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PixCharge::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sale_id'=>Sale::factory(),
            'gateway_id'=>Gateway::SAFE2PAY_PRODUCTION_ID,
            'txid'=>$this->faker->randomNumber(6),            
            'qrcode'=>$this->faker->sha256(),
            'qrcode_image'=>$this->faker->imageUrl(60,60),
            'status'=>'ATIVA',
            'expiration_date'=>Carbon::now()->addDay(),
        ];
    }    

    
}
