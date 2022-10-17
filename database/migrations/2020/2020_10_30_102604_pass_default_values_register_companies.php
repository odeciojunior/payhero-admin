<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PassDefaultValuesRegisterCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table
                ->integer("credit_card_release_money_days")
                ->default("15")
                ->after("gateway_release_money_days")
                ->change();
            $table
                ->string("boleto_release_money_days")
                ->default("2")
                ->after("active_flag")
                ->change();
            $table
                ->string("credit_card_tax")
                ->default("6.5")
                ->after("gateway_tax")
                ->change();
            $table
                ->string("boleto_tax")
                ->default("6.9")
                ->after("gateway_tax")
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table
                ->integer("credit_card_release_money_days")
                ->nullable()
                ->change();
            $table
                ->string("boleto_release_money_days")
                ->nullable()
                ->change();
            $table
                ->string("credit_card_tax")
                ->nullable()
                ->change();
            $table
                ->string("boleto_tax")
                ->nullable()
                ->change();
        });
    }
}
