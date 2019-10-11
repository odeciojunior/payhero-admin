<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableSetPaymentTaxesVariables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->string('boleto_tax')->default('5.9');
            $table->string('credit_card_tax')->default('5.9');
            $table->integer('credit_card_release_money_days')->default(30);
            $table->integer('boleto_release_money_days')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('boleto_tax');
            $table->dropColumn('credit_card_tax');
            $table->dropColumn('credit_card_release_money_days');
            $table->dropColumn('boleto_release_money_days');
        });
    }
}
