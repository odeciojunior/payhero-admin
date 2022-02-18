<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTransfeeraRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfeera_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('withdrawal_id');
            $table->foreign('withdrawal_id')->references('id')->on('withdrawals');
            $table->json('sent_data')->nullable();
            $table->json('response')->nullable();
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
        Schema::dropIfExists('transfeera_requests');
    }
}
