<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCardFlagsToGatewayFlagsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('card_flags', function(Blueprint $table) {
            $table->dropForeign(['gateway_id']);
        });
        Schema::table('card_flags', function(Blueprint $table) {
            $table->rename('gateway_flags');
        });
        Schema::table('gateway_flags', function(Blueprint $table) {
            $table->foreign('gateway_id')->references('id')->on('gateways');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('gateway_flags', function(Blueprint $table) {
            $table->dropForeign(['gateway_id']);
        });
        Schema::table('gateway_flags', function(Blueprint $table) {
            $table->rename('card_flags');
        });
        Schema::table('card_flags', function(Blueprint $table) {
            $table->foreign('gateway_id')->references('id')->on('gateways');
        });
    }
}
