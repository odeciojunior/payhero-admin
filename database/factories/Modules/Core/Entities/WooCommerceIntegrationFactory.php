<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;
use Modules\Core\Entities\WooCommerceIntegration;

class WooCommerceIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WooCommerceIntegration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {        
        return [
            'project_id'=>Project::factory(),
            'user_id'=>User::DEMO_ID,                        
            'token_user'=>$this->faker->sha256(),
            'token_pass'=>$this->faker->md5(),
            'url_store'=>$this->faker->url(),
            'status'=>2,
        ];
    }    
}
