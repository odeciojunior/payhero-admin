<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Checkout;
use Modules\Core\Services\FoxUtils;

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
        
        $OS = $this->getRandomOperationalSystem();

        return [
            'status' => 'accessed',
            'status_enum' => Checkout::STATUS_ACCESSED,
            'project_id' => 1,
            'affiliate_id' =>function (array $attributes) {
                return FoxUtils::getCookieAffiliate($attributes['project_id']);
            } ,
            'ip' => $this->faker->ipv4,
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
            'browser' => $this->getRandomBrowser(),
            'utm_source' => $this->faker->word(),
            'utm_medium' => null,
            'utm_campaign' => null,
            'utm_term' => null,
            'utm_content' => null,
            'src' => $this->getRandomSrc(),
            'template_type' => '',
        ];
    }

    public function getRandomOperationalSystem(){
        
        $operationalSystem = [
            [ 'so'=>"iOS",'version'=>rand(8,15).'_'.rand(0,9).'_'.rand(0,9),'is_mobile'=>true,'enum'=>Checkout::OPERATIONAL_SYSTEM_IOS],
            [ 'so'=>"ChromeOS", 'version'=>rand(8172,14469).'.'.rand(45,99).'.'.rand(0,5), 'is_mobile'=>false,'enum'=>Checkout::OPERATIONAL_SYSTEM_CHROME],
            [ 'so'=>"OpenBSD",'version'=>'','is_mobile'=>false,'enum'=>Checkout::OPERATIONAL_SYSTEM_LINUX],
            [ 'so'=>"BlackBerryOS", 'version'=> '10.0.9.2372', 'is_mobile'=>false,'enum'=>Checkout::OPERATIONAL_SYSTEM_BLACK_BERRY],
            [ 'so'=>"AndroidOS", 'version'=> rand(4,12).'.'.rand(0,9).'.'.rand(0,9),'is_mobile'=>true,'enum'=>Checkout::OPERATIONAL_SYSTEM_ANDROID],
            [ 'so'=>'Windows','version'=>rand(5,11).'.'.rand(0,9),'is_mobile'=>false,'enum'=>Checkout::OPERATIONAL_SYSTEM_WINDOWS],
            [ 'so'=>'OS X','version'=>rand(10,12).'_'.rand(0,15).'_'.rand(0,9), 'is_mobile'=>false,'enum'=>Checkout::OPERATIONAL_SYSTEM_UNKNOWN]
        ];

        return Arr::random($operationalSystem);
    }

    public function getRandomBrowser(){
        $browsers = [
                "Chrome - ".rand(99,102).'.'.rand(0,1).'.'.rand(1150,9999).'.'.rand(0,99),
                "Opera - ".rand(10,90).'.'.rand(0,3).'.'.rand(1754,3606).'.'.rand(61072,65175),
                "Edge - 101.0.4951.64".rand(97,102).'.'.rand(0,1).'.'.rand(1210,4951).'.'.rand(10,90),
                "Opera Mini - 4.4.33576".rand(2,4).'.'.rand(0,4).'.'.rand(17540,33576),
                "Firefox - ".rand(98,101).'.'.rand(0,3),
                "Safari - 15.4.1".rand(13,15).'.'.rand(0,4).'.'.rand(0,9),
                "UCBrowser - 11.3.5.908".rand(8,11).'.'.rand(0,3).'.'.rand(0,9).'.'.rand(100,908),               
        ]; 
        
        return Arr::random($browsers);
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
