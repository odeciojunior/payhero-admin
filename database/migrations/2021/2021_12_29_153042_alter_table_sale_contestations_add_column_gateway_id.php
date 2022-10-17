<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSaleContestationsAddColumnGatewayId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sale_contestations", function (Blueprint $table) {
            //gateway default Getnet
            $table
                ->unsignedBigInteger("gateway_id")
                ->default(15)
                ->after("sale_id");
            $table
                ->foreign("gateway_id")
                ->references("id")
                ->on("gateways");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sale_contestations", function (Blueprint $table) {
            $table->dropForeign("sale_contestations_gateway_id_foreign");
            $table->dropColumn("gateway_id");
        });
    }
}
