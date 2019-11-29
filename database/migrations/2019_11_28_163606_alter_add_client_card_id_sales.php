<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddClientCardIdSales extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->unsignedBigInteger('client_card_id')->nullable()->after('client_id');
        });
        Schema::table('sales', function(Blueprint $table) {
            $table->foreign('client_card_id')->references('id')->on('client_cards');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->dropColumn('client_card_id');
        });
    }
}
