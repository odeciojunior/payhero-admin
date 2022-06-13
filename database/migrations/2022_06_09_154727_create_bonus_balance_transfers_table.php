<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusBalanceTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_balance_transfers', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('bonus_balance_id')->unsigned();
            $table->foreign('bonus_balance_id')->references('id')->on('bonus_balances');  

            $table->integer('type')->default(1);
            $table->integer('value');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonus_balance_transfers');
    }
}
