<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSuspectBotCheckout extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('suspect_bots', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('checkout_id')->index();
            $table->timestamps();
        });

        Schema::table('suspect_bots', function(Blueprint $table) {
            $table->foreign('checkout_id')->references('id')->on('checkouts');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_suspect_bot_checkout');
    }
}
