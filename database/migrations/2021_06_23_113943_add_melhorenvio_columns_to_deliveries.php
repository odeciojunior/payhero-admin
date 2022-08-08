<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMelhorenvioColumnsToDeliveries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("deliveries", function (Blueprint $table) {
            $table
                ->integer("melhorenvio_carrier_id")
                ->nullable()
                ->after("type");
            $table
                ->string("melhorenvio_order_id")
                ->nullable()
                ->after("melhorenvio_carrier_id")
                ->index();
        });

        // drop the disused columns
        Schema::table("deliveries", function (Blueprint $table) {
            $table->dropForeign("entregas_transportadora_foreign");
            $table->dropColumn(["carrier_id", "id_order_carrier", "status_carrier", "tracking_code"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("deliveries", function (Blueprint $table) {
            $table->dropColumn(["melhorenvio_carrier_id", "melhorenvio_order_id"]);
        });

        // recreate the disused columns
        Schema::table("deliveries", function (Blueprint $table) {
            $table
                ->unsignedInteger("carrier_id")
                ->nullable()
                ->after("complement")
                ->index("entregas_transportadora_foreign");
            $table
                ->bigInteger("id_order_carrier")
                ->nullable()
                ->after("carrier_id");
            $table
                ->string("status_carrier")
                ->nullable()
                ->after("id_order_carrier");
            $table
                ->string("tracking_code")
                ->nullable()
                ->after("status_carrier");
        });
    }
}
