<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTableGatewayConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("gateway_configs", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table
                ->unsignedBigInteger("gateway_id")
                ->index()
                ->nullable();
            $table->string("type");
            $table->tinyInteger("type_enum");

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table("gateway_configs", function (Blueprint $table) {
            $table
                ->foreign("gateway_id")
                ->references("id")
                ->on("gateways");
        });

        $sql = "INSERT INTO gateway_configs (name, gateway_id, type, type_enum) ";
        $sql .= "VALUES('cielo', 5, 'credit_card', 1)";

        DB::select($sql);

        $sql = "INSERT INTO gateway_configs (name, gateway_id, type, type_enum) ";
        $sql .= "VALUES('cielo', 5, 'billet', 2)";

        DB::select($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("gateway_configs");
    }
}
