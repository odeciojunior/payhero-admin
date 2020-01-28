<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleAntifraudResultsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('sale_antifraud_results', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('antifraud_id');
            $table->json('send_data');
            $table->json('antifraud_result')->nullable();
            $table->string('status', 255);
            $table->json('translated_codes')->nullable();
            $table->json('antifraud_exceptions')->nullable();
            $table->timestamps();
        });
        Schema::table('sale_antifraud_results', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->foreign('antifraud_id')->references('id')->on('antifrauds');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sale_antifraud_results', function(Blueprint $table) {
            $table->dropForeign(["sale_id"]);
            $table->dropForeign(["antifraud_id"]);
        });
        Schema::dropIfExists('sale_antifraud_results');
    }
}
