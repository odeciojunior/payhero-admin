<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntifraudPostbacks extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('antifraud_postbacks', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id')->index()->nullable();
            $table->unsignedBigInteger('antifraud_id')->index()->nullable();
            $table->json('data');
            $table->unsignedTinyInteger('processed_flag')->index()->default(0); // 0-no , 1-yes
            $table->unsignedTinyInteger('postback_valid_flag')->index()->default(0); // 0-no , 1-yes
            $table->json('machine_result')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('antifraud_postbacks');
    }
}
