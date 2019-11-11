<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntifraudsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('antifrauds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('api', 255);
            $table->unsignedInteger('antifraud_api_enum');
            $table->string('environment', 50);
            $table->string('client_id', 255);
            $table->string('client_secret', 255);
            $table->string('merchant_id', 255);
            $table->boolean('available_flag')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('antifrauds');
    }
}
