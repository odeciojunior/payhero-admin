<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;

class AffiliateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Affiliate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {        
        return [
            'user_id'=>User::factory(),
            'project_id'=>Project::factory(),
            'company_id'=>Company::DEMO_ID,
            'percentage'=>rand(10,45),
            'status_enum'=>Affiliate::STATUS_ACTIVE,
            'suport_phone_verified'=>0,
            'suport_phone'=>$this->faker->e164PhoneNumber(),
            'suport_contact_verified'=>1,
            'suport_contact'=>0,
            'order_priority'=>0,
        ];
    }    

}
