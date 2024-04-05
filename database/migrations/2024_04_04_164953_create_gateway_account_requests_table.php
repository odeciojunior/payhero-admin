<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("gateway_account_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("gateway_account_requests_foreign");
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->index("gateway_account_requests_gateway_id_foreign");

            $table->json("send_data")->nullable();
            $table->json("gateway_result")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("gateway_account_requests_created_at_IDX");

            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");

            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");

            $table->timestamp("updated_at")->nullable();
        });

        Schema::table("gateways_companies_credentials", function (Blueprint $table) {
            $table
                ->string("gateway_user_token", 100)
                ->nullable()
                ->default(null)
                ->after("gateway_api_key");
            $table
                ->string("gateway_contact_id", 100)
                ->nullable()
                ->default(null)
                ->after("gateway_user_token");
        });

        Schema::table("company_bank_accounts", function (Blueprint $table) {
            $table
                ->enum("account_type", ["CORRENTE", "POUPANCA"])
                ->nullable()
                ->default(null)
                ->after("account_digit");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("gateway_account_requests");
    }
};
