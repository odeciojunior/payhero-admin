<?php

namespace Database\Factories\Modules\Core\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\MelhorenvioIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;

class MelhorenvioIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MelhorenvioIntegration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {        
        return [
            
            'user_id'=>User::DEMO_ID,
            'name'=>$this->faker->name(),
            'access_token'=>$this->faker->md5(),
            'refresh_token'=>$this->faker->md5(),
            'expiration'=>Carbon::now()->addMonth(3),
            'completed'=>1,
        ];
    }    
}
