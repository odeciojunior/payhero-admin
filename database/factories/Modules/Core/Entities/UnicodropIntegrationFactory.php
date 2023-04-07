<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UnicodropIntegration;
use Modules\Core\Entities\User;

class UnicodropIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UnicodropIntegration::class;

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
            "token" => $this->faker->md5(),
            "billet_generated" => rand(0, 1),
            "billet_paid" => rand(0, 1),
            "credit_card_refused" => rand(0, 1),
            "credit_card_paid" => rand(0, 1),
            "abandoned_cart" => rand(0, 1),
            "pix" => rand(0, 1),
        ];
    }
}
