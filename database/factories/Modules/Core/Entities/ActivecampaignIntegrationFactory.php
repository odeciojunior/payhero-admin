<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;

class ActivecampaignIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivecampaignIntegration::class;

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
            'api_url'=>$this->faker->url(),
            'api_key'=>$this->faker->sha256()
        ];
    }    
}
