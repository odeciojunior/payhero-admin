<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCompaniesAddTaxesColumns extends Migration
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
                ->string("boleto_rule")
                ->default("percent")
                ->after("gateway_tax");
            $table
                ->string("boleto_tax")
                ->default("6.9")
                ->after("gateway_tax");
            $table
                ->string("pix_rule")
                ->default("percent")
                ->after("gateway_tax");
            $table
                ->string("pix_tax")
                ->default("6.9")
                ->after("gateway_tax");
            $table
                ->string("credit_card_rule")
                ->default("percent")
                ->after("gateway_tax");
            $table
                ->string("credit_card_tax")
                ->default("6.9")
                ->after("gateway_tax");
            $table
                ->boolean("tax_default")
                ->default(true)
                ->after("gateway_tax");
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
            $table->dropColumn(["boleto_rule"]);
            $table->dropColumn(["boleto_tax"]);
            $table->dropColumn(["pix_rule"]);
            $table->dropColumn(["pix_tax"]);
            $table->dropColumn(["credit_card_rule"]);
            $table->dropColumn(["credit_card_tax"]);
            $table->dropColumn(["tax_default"]);
        });
    }
}
