<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("api_tokens", function (Blueprint $table) {
            $table
                ->unsignedInteger("company_id")
                ->after("user_id")
                ->nullable();

            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("api_tokens", function (Blueprint $table) {
            $table->dropForeign(["company_id"]);
            $table->dropColumn("company_id");
        });
    }
}
