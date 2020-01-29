<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnNeighborhoodTableDelivery extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function(Blueprint $table) {
            $table->string('neighborhood', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('deliveries', function(Blueprint $table) {
            $table->string('neighborhood', 60)->change();
        });
    }
}
