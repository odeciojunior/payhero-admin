<?php

namespace Database\Factories\Modules\Core\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Checkout;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\FoxUtilsFakeService;

class CheckoutFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Core\Entities\Checkout::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        
        $OS = FoxUtilsFakeService::getRandomOperationalSystem();

        return [
            'status' => 'accessed',
            'status_enum' => Checkout::STATUS_ACCESSED,
            'project_id' => 1,
            'affiliate_id' =>function (array $attributes) {
                return FoxUtils::getCookieAffiliate($attributes['project_id']);
            } ,
            'ip' => $this->faker->ipv4,
            'id_state'=>FoxUtilsFakeService::getRandomUf(),
            'country' => '',
            'city' => '',
            'state' => '',
            'state_name' => '',
            'zip_code' => '',
            'currency' => '',
            'lat' => $this->faker->latitude(),
            'lon' => $this->faker->longitude(),
            'is_mobile' => $OS['is_mobile'],
            'operational_system' =>$OS['so'].' - '.$OS['version'],
            'os_enum' => $OS['enum'],
            'browser' => FoxUtilsFakeService::getRandomBrowser(),
            'utm_source' => $this->faker->word(),
            'utm_medium' => null,
            'utm_campaign' => null,
            'utm_term' => null,
            'utm_content' => null,
            'src' => $this->getRandomSrc(),
            'template_type' => 0,
            'created_at'=>now(),
            'updated_at'=>now()
        ];
    }    

    public function getRandomSrc(){
        $srcs = [                   
            "SMS6",
            "RECWHATS",
            "RMKTMÃES",
            "google1",
            "knxface8kc1",
            "tiktoknx3vemvem",
            "tiktok2knx",
            "tiktok4knx",
            "tiktok3knx",
            "tiktok1knx"
        ];
        return Arr::random($srcs);
    }
}
