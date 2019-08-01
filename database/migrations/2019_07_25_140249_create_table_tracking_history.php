<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTrackingHistory extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_histories', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('delivery_id')->index();
            $table->string('tracking_code');

            $table->timestamps();
        });

        Schema::table('tracking_histories', function(Blueprint $table) {
            $table->foreign('delivery_id')->references('id')->on('deliveries');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_histories', function(Blueprint $table) {
            $table->dropForeign(['delivery_id']);
        });

        Schema::dropIfExists('tracking_histories');
    }
}
