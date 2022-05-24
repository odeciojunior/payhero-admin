<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleInformation;
use Modules\Core\Services\FoxUtilsFakeService;

class SaleInformationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaleInformation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $OS = FoxUtilsFakeService::getRandomOperationalSystem();
        return [
            "sale_id"=>Sale::factory(),
            "operational_system"=>$OS['so'].' - '.$OS['version'],
            "browser"=>FoxUtilsFakeService::getRandomBrowser(),
            "browser_fingerprint"=>$this->faker->isbn13(),
            "browser_token"=>null,
            "ip"=>$this->faker->ipv4(),
            "customer_name"=>$this->faker->name(),
            "customer_email"=>$this->faker->email(),
            "customer_phone"=>$this->faker->e164PhoneNumber(),
            "customer_identification_number"=>substr($this->faker->ean13(),-11),
            "project_name"=>null,
            "transaction_amount"=>null,
            "country"=>$this->faker->countryCode(),
            "zip_code"=>$this->faker->randomNumber(8),
            "state"=>FoxUtilsFakeService::getRandomUf(),
            "city"=>$this->faker->city(),
            "district"=>$this->faker->streetName(),
            "street_name"=>$this->faker->streetName(),
            "street_number"=>$this->faker->buildingNumber(),
            "card_token"=>$this->faker->md5(),
            "card_brand"=>$this->faker->creditCardType(),
            "installments"=>null,
            "first_six_digits"=>null,
            "last_four_digits"=>null,
        ];
    }    
}
