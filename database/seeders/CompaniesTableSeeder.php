<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userStore = User::whereNull("is_cloudfox")->first();
        $company = Company::create([
            "user_id" => $userStore->id,
            "fantasy_name" => "Azcend LTDA",
            "document" => "05054642000114",
            "zip_code" => "37350-000",
            "country" => "brazil",
            "state" => "SP",
            "city" => "BARUERI",
            "street" => "AV MARCOS PENTEADO DE ULHOA RODRIGUES",
            "complement" => "ANDAR 8 EDIF JACARANDA TORRE 1",
            "neighborhood" => "TAMBORE",
            "number" => 939,
            "support_email" => "edwingarcia@azcend.com.br",
            "support_telephone" => "+5566996866082",
            "cielo_balance" => 0,
            "asaas_balance" => 0,
            "vega_balance" => 0,
            "address_document_status" => 3,
            "contract_document_status" => 3,
            "capture_transaction_enabled" => 1,
            "account_type" => 1,
            "credit_card_release_money_days" => 30,
            "bank_slip_release_money_days" => 3,
            "pix_release_money_days" => 1,
            "situation" => json_encode([
                "situation" => "active",
                "situation_enum" => 1,
                "date_check_situation" => now(),
            ]),
        ]);

        $userStore->update([
            "company_default" => $company->id,
        ]);
    }
}
