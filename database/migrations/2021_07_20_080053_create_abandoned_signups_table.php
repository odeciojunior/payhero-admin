<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbandonedSignupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("abandoned_signups", function (Blueprint $table) {
            $table->bigIncrements("id");

            $table->integer("last_step")->default(0);
            $table->integer("status")->default(0);

            //Person
            $table->string("email")->unique();
            $table->string("name")->nullable();
            $table->string("document")->unique();
            $table->string("phone")->nullable();

            //Leads
            $table->string("monthly_income")->nullable();

            $table->json("niche")->nullable();

            $table->string("website_url")->nullable();

            $table->string("gateway")->nullable();

            $table->json("ecommerce")->nullable();

            $table->json("cloudfox_referer")->nullable();

            //Person address
            $table->string("zip_code")->nullable();
            $table->string("country")->nullable();
            $table->string("state")->nullable();
            $table->string("city")->nullable();
            $table->string("district")->nullable();
            $table->string("street")->nullable();
            $table->string("number")->nullable();
            $table->string("complement")->nullable();

            //Company address
            $table->string("company_document")->nullable();
            $table->string("company_zip_code")->nullable();
            $table->string("company_country")->nullable();
            $table->string("company_state")->nullable();
            $table->string("company_city")->nullable();
            $table->string("company_district")->nullable();
            $table->string("company_street")->nullable();
            $table->string("company_number")->nullable();
            $table->string("company_complement")->nullable();

            //bank account
            $table->string("bank")->nullable();
            $table->string("agency")->nullable();
            $table->string("agency_digit")->nullable();
            $table->string("account")->nullable();
            $table->string("account_digit")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("abandoned_signups");
    }
}
