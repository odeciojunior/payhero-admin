<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Company;

class SetTaxCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = (new Company())->get();

        foreach ($companies as $company) {
            if (!empty($company->user->boleto_tax)) {
                $company->update([
                    "boleto_tax" => $company->user->boleto_tax,
                    "credit_card_tax" => $company->user->credit_card_tax,
                    "installment_tax" => $company->user->installment_tax,
                    "credit_card_release_money_days" => $company->user->credit_card_release_money_days,
                    "boleto_release_money_days" => $company->user->boleto_release_money_days,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
