<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCompaniesDropTaxs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table->dropColumn([
                "credit_card_release_money_days",
                "boleto_release_money_days",
                "credit_card_tax",
                "boleto_tax",
            ]);

            $table->renameColumn("company_document", "document");
            $table->renameColumn("document_number", "extra_document");
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
            $table->integer("credit_card_release_money_days")->default(15);
            $table->string("boleto_release_money_days", 255)->default("2");
            $table->integer("credit_card_tax")->default(6.5);
            $table->string("boleto_tax", 255)->default("6.9");

            $table->renameColumn("document", "company_document");
            $table->renameColumn("extra_document", "document_number");
        });
    }
}
