<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCompaniesTableForCreditCardReleaseDefaults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->time('credit_card_release_time')->default('17:00:00')->change();
            $table->boolean('credit_card_release_on_weekends')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->time('credit_card_release_time')->default(null)->change();
            $table->boolean('credit_card_release_on_weekends')->default(true)->change();
        });
    }
}