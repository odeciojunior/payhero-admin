<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;

use Modules\Core\Entities\Project;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\User;

class ShopifyIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ShopifyIntegration::class;

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
            "token" => $this->faker->sha256(),
            "shared_secret" => $this->faker->sha256(),
            "url_store" => $this->faker->url(),
            "theme_type" => 1,
            "theme_name" => "Debut",
            "theme_file" => "sections/cart-template.liquid",
            "theme_html" => null,
            "layout_theme_html" => null,
            "status" => ShopifyIntegration::STATUS_APPROVED,
            "skip_to_cart" => 0,
        ];
    }
}
