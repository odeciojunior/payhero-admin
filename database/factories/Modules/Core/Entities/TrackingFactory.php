<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;

class TrackingFactory extends Factory
{
 /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tracking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_plan_sale_id'=>null,
            'sale_id'=>Sale::factory(),
            'product_id'=>null,
            'amount'=>null,
            'delivery_id'=>null,
            'tracking_code'=>'L'.$this->faker->isbn10(),
            'tracking_status_enum'=>$this->getRandomStatus(),
            'system_status_enum'=>Tracking::SYSTEM_STATUS_VALID, 
        ];
    }

    public function getRandomStatus()
    {
        $status = [
            Tracking::STATUS_POSTED,
            Tracking::STATUS_DISPATCHED,
            Tracking::STATUS_DELIVERED,
            // Tracking::STATUS_OUT_FOR_DELIVERY,
            // Tracking::STATUS_EXCEPTION
        ];
        return Arr::random($status);
    }

    public function getRandomSystemStatus()
    {
        $systemStatus = [
            Tracking::SYSTEM_STATUS_VALID, 
            Tracking::SYSTEM_STATUS_NO_TRACKING_INFO,
            Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER,
            Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE,
            Tracking::SYSTEM_STATUS_DUPLICATED,
            Tracking::SYSTEM_STATUS_CHECKED_MANUALLY
        ];
        return Arr::random($systemStatus);
    }
   
}