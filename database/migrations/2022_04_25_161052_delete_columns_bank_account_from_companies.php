<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteColumnsBankAccountFromCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('bank');
            $table->dropColumn('agency');
            $table->dropColumn('agency_digit');
            $table->dropColumn('account');
            $table->dropColumn('account_digit');
            $table->dropColumn('has_pix_key');
            $table->dropColumn('pix_key_situation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
