<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table->dropColumn("braspag_merchant_homolog_id");
            $table->dropColumn("braspag_merchant_id");
            $table->dropColumn("braspag_status");

            $table->dropColumn("founding_date");
            $table->dropColumn("social_value");
            $table->dropColumn("federal_registration_status_date");
            $table->dropColumn("federal_registration_status");
            $table->dropColumn("monthly_gross_income");
            $table->dropColumn("business_entity_type");
            $table->dropColumn("economic_activity_classification_code");
            $table->dropColumn("state_fiscal_document_number");
            $table->dropColumn("patrimony");
            $table->dropColumn("business_website");
            $table->dropColumn("shortened_descriptor");
            $table->dropColumn("statement_descriptor");

            $table->integer("annual_income")->nullable();
        });

        DB::statement("ALTER TABLE companies MODIFY COLUMN created_at timestamp AFTER annual_income");
        DB::statement("ALTER TABLE companies MODIFY COLUMN updated_at timestamp AFTER created_at");
        DB::statement("ALTER TABLE companies MODIFY COLUMN deleted_at timestamp AFTER updated_at");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table->string("braspag_merchant_homolog_id", 255)->default(6.9);
            $table->string("braspag_merchant_id", 255)->nullable();
            $table->unsignedTinyInteger("braspag_status")->nullable();

            $table->date("founding_date")->nullable();
            $table->unsignedTinyInteger("social_value")->nullable();
            $table->date("federal_registration_status_date")->nullable();
            $table->unsignedTinyInteger("federal_registration_status")->nullable();
            $table->unsignedTinyInteger("monthly_gross_income")->nullable();
            $table->string("business_entity_type", 255)->nullable();
            $table->string("economic_activity_classification_code", 255)->nullable();
            $table->string("state_fiscal_document_number", 255)->nullable();
            $table->unsignedTinyInteger("patrimony")->nullable();
            $table->string("business_website", 255)->nullable();
            $table->string("shortened_descriptor", 255)->nullable();
            $table->string("statement_descriptor", 255)->nullable();

            $table->dropColumn("annual_income");
        });
    }
}
