<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\User;

class ReportanaIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportanaIntegration::class;

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
            'url_api'=>$this->faker->url(),            
            'billet_generated'=>rand(0,1),
            'billet_paid'=>rand(0,1),
            'credit_card_refused'=>rand(0,1),
            'credit_card_paid'=>rand(0,1),
            'abandoned_cart'=>rand(0,1),    
            'pix_generated'=>rand(0,1),
            'pix_paid'=>rand(0,1),
        ];
    }    
}
