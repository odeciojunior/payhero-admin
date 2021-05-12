<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGatewayPostbacksAddPayPostbackFlagTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->boolean('pay_postback_flag')->default(false)->after('postback_valid_flag');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->dropColumn('pay_postback_flag');
        });
    }
}
