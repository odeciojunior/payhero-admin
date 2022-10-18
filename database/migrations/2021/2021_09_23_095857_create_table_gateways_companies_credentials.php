<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGatewaysCompaniesCredentials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("gateways_companies_credentials", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("company_id");
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
            $table->unsignedBigInteger("gateway_id");
            $table
                ->foreign("gateway_id")
                ->references("id")
                ->on("gateways");
            $table
                ->tinyInteger("gateway_status")
                ->nullable()
                ->default(null);
            $table
                ->string("gateway_subseller_id", 100)
                ->nullable()
                ->default(null);
            $table
                ->string("gateway_api_key", 100)
                ->nullable()
                ->default(null);
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
        Schema::dropIfExists("gateways_companies_credentials");
    }
}
