<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAsaasTransfers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asaas_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("withdrawal_id")->nullable();
            $table->foreign('withdrawal_id')->references('id')->on('withdrawals');
            $table->string('transaction_id',50)->nullable()->default(null);
            $table->integer('value');
            $table->string('status',15)->nullable()->default(null);
            $table->json('sent_data')->nullable()->default(null);
            $table->json('response')->nullable()->default(null);
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
        Schema::dropIfExists('asaas_transfers');
    }
}
