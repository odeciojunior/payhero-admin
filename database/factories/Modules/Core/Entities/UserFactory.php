<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Support\Str;
use Modules\Core\Entities\User;
use Modules\Core\Services\FoxUtilsFakeService;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->name(),
            'email'=>$this->faker->email(),
            'email_verified'=>true,
            'status'=>User::STATUS_ACTIVE,
            'password'=>$this->faker->sha256(),
            'remember_token'=>null,
            'cellphone'=>$this->faker->e164PhoneNumber(),
            'cellphone_verified'=>true,
            'document'=>substr($this->faker->ean13(),-11),
            'zip_code'=>$this->faker->randomNumber(9),
            'country'=>'brazil',
            'state'=>FoxUtilsFakeService::getRandomUf(),
            'city'=>null,
            'neighborhood'=>null,
            'street'=>null,
            'number'=>null,
            'complement'=>null,
            'photo'=>null,
            'date_birth'=>null,
            'address_document_status'=>User::DOCUMENT_STATUS_APPROVED,
            'personal_document_status'=>User::DOCUMENT_STATUS_APPROVED,
            'invites_amount'=>0,
            'last_login'=>now(),
            'account_owner_id'=>null,
            'level'=>1,
            'total_commission_value'=>0,
            'block_attendance_balance'=>0,
        ];
    }    

    
}
