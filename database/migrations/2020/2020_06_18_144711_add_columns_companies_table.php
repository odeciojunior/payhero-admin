<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsCompaniesTable extends Migration
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
                ->unsignedInteger("patrimony")
                ->nullable()
                ->after("order_priority");
            $table
                ->string("state_fiscal_document_number", 255)
                ->nullable()
                ->after("patrimony");
            $table
                ->string("business_entity_type", 255)
                ->nullable()
                ->after("state_fiscal_document_number");
            $table
                ->string("economic_activity_classification_code", 255)
                ->nullable()
                ->after("business_entity_type");
            $table
                ->unsignedInteger("monthly_gross_income")
                ->nullable()
                ->after("economic_activity_classification_code");
            $table
                ->unsignedInteger("federal_registration_status")
                ->nullable()
                ->after("monthly_gross_income");
            $table
                ->date("founding_date")
                ->nullable()
                ->after("federal_registration_status");
            $table
                ->unsignedInteger("subseller_getnet_id")
                ->nullable()
                ->after("founding_date");
            $table
                ->unsignedInteger("account_type")
                ->nullable()
                ->after("subseller_getnet_id");
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
            $table->dropColumn([
                "patrimony",
                "state_fiscal_document_number",
                "business_entity_type",
                "economic_activity_classification_code",
                "monthly_gross_income",
                "federal_registration_status",
                "founding_date",
                "subseller_getnet_id",
                "account_type",
            ]);
        });
    }
}
