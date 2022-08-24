<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;

use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;

class ApiTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApiToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {        
        return [
            'user_id'=>User::DEMO_ID,
            'company_id'=>Company::DEMO_ID,
            'token_id'=>$this->faker->sha256(),
            'access_token'=>$this->faker->sha256(),
            'scopes'=>'["sale","product"]',
            'integration_type_enum'=>ApiToken::INTEGRATION_TYPE_EXTERNAL,
            'description'=>$this->faker->sentence(rand(6,10)),
            'postback'=>$this->faker->url()
        ];
    }    

}
