<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "carrier_id" => null,
            "photo" => $this->faker->imageUrl(1024, 728, "dogs"),
            "visibility" => "private",
            "status" => Project::STATUS_ACTIVE,
            "name" => $this->faker->sentence(rand(6, 10)),
            "description" => $this->faker->paragraph(1),
            "percentage_affiliates" => 0,
            "terms_affiliates" => null,
            "status_url_affiliates" => 0,
            "commission_type_enum" => 2,
            "url_page" => $this->faker->domainName(),
            "automatic_affiliation" => 0,
            "shopify_id" => null,
            "woocommerce_id" => null,
            "cookie_duration" => 0,
            "url_cookies_checkout" => null,
            "boleto_redirect" => null,
            "card_redirect" => null,
            "pix_redirect" => null,
            "analyzing_redirect" => null,
            "cost_currency_type" => 1,
            "discount_recovery_status" => 0,
            "discount_recovery_value" => 0,
            "reviews_config_icon_type" => "star",
            "reviews_config_icon_color" => $this->faker->hexcolor(),
            "notazz_configs" => null,
        ];
    }
}
