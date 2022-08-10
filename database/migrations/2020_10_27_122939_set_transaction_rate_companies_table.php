<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Company;

class SetTransactionRateCompaniesTable extends Migration
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
            if (!empty($company->user->transaction_rate)) {
                $company->update([
                    "transaction_rate" => $company->user->transaction_rate,
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
