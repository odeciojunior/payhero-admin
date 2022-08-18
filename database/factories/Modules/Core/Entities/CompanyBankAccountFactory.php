<?php

namespace Database\Factories\Modules\Core\Entities;

use Modules\Core\Entities\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Entities\CompanyBankAccount;

class CompanyBankAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanyBankAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_id'=>Company::DEMO_ID,
            'transfer_type'=>Company::DEMO_ID,
            'type_key_pix'=>'EMAIL',
            'key_pix'=>$this->faker->email(),            
            'is_default'=>true,
            'status'=>'VERIFIED'            
        ];
    }    

}
