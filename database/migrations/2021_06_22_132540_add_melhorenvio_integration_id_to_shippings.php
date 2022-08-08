<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMelhorenvioIntegrationIdToShippings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("shippings", function (Blueprint $table) {
            $table
                ->unsignedBigInteger("melhorenvio_integration_id")
                ->nullable()
                ->after("zip_code_origin");
        });

        Schema::table("shippings", function (Blueprint $table) {
            $table
                ->foreign("melhorenvio_integration_id")
                ->references("id")
                ->on("melhorenvio_integrations");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("shippings", function (Blueprint $table) {
            $table->dropColumn("melhorenvio_integration_id");
        });
    }
}
