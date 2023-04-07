<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;

use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;

class NotazzIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NotazzIntegration::class;

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
            "token_webhook" => $this->faker->sha256(),
            "token_api" => $this->faker->sha256(),
            "token_logistics" => null,
            "start_date" => now(), //data inicial da geracao das notas
            "retroactive_generated_date" => now(), //data da geração das notas retroativas
            "invoice_type" => 1,
            "pending_days" => 30,
            "generate_zero_invoice_flag" => rand(0, 1),
            "discount_plataform_tax_flag" => 0,
            "active_flag" => 1,
        ];
    }
}
