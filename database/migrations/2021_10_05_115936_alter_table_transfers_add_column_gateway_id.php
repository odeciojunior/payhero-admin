<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableTransfersAddColumnGatewayId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('gateway_id')->nullable()->after('company_id');
            $table->foreign('gateway_id')->references('id')->on('gateways');
        });

        DB::statement('update transfers set gateway_id = 5 where customer_id is null');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfers', function(Blueprint $table) {
            $table->dropForeign(['gateway_id']);
            $table->dropColumn('gateway_id');
        });
    }
}
