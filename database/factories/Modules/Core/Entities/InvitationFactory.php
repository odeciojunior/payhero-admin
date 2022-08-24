<?php

namespace Database\Factories\Modules\Core\Entities;

use Carbon\Carbon;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invitation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'invite'=>User::DEMO_ID,
            'user_invited'=>User::factory(),
            'company_id'=>Company::DEMO_ID,            
            'email_invited'=>$this->faker->email(),
            'status'=>rand(1,3),
            'register_date'=>now(),
            'expiration_date'=>Carbon::now()->addDays(3)->format('Y-m-d'),
            'parameter'=>null,
        ];
    }    

    
}
