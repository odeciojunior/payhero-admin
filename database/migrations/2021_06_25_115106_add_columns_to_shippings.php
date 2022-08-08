<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToShippings extends Migration
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
                ->boolean("receipt")
                ->default(false)
                ->after("melhorenvio_integration_id");
            $table
                ->boolean("own_hand")
                ->default(false)
                ->after("receipt");
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
            $table->dropColumn(["receipt", "own_hand"]);
        });
    }
}
