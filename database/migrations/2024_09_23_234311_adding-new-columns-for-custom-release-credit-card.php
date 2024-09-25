<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("companies",function(Blueprint $table){
            $table->tinyInteger("credit_card_release_on_weekends")->default(true)->after("credit_card_release_money_days");
            $table->time("credit_card_release_time")->nullable()->default(null)->after("credit_card_release_on_weekends");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
