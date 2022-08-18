<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\ConvertaxIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;

class ConvertaxIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ConvertaxIntegration::class;

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
            'link'=>$this->faker->url(),
            'value'=>$this->faker->randomNumber()*100,
            'boleto_generated'=>rand(0,1),
            'boleto_paid'=>rand(0,1),
            'credit_card_refused'=>rand(0,1),
            'credit_card_paid'=>rand(0,1),
            'abandoned_cart'=>rand(0,1),            
        ];
    }    
}
