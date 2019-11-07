<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSaleTableRenameGatewayIdColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->renameColumn('gateway_id', 'gateway_transaction_id');
            $table->unsignedBigInteger('gateway_id')->index();
        });

        Schema::table('sales', function(Blueprint $table) {
            $table->foreign('gateway_id')->references('id')->on('gateways');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->dropForeign(['gateway_id']);
        });

        Schema::table('sales', function(Blueprint $table) {
            $table->dropColumn('gateway_id');
        });

        Schema::table('sales', function(Blueprint $table) {
            $table->renameColumn('gateway_transaction_id', 'gateway_id');
        });
    }
}
