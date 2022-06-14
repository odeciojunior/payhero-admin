<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_balances', function (Blueprint $table) {
            $table->id();
            
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');            

            $table->integer('total_value')->default(0);

            $table->integer('current_value')->default(0);

            $table->date('expires_at');

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
        Schema::dropIfExists('bonus_balances');
    }
}
