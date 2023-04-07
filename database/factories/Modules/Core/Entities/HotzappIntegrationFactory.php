<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;

class HotzappIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HotzappIntegration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "project_id" => Project::DEMO_ID,
            "user_id" => User::DEMO_ID,
            "link" => $this->faker->url(),
            "boleto_generated" => rand(0, 1),
            "boleto_paid" => rand(0, 1),
            "credit_card_refused" => rand(0, 1),
            "credit_card_paid" => rand(0, 1),
            "abandoned_cart" => rand(0, 1),
        ];
    }
}
