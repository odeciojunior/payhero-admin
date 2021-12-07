<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAsaasTransfersAddColumnTransferId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asaas_transfers',function(Blueprint $table){
            $table->unsignedBigInteger("transfer_id")->nullable()->after('withdrawal_id');
            $table->foreign('transfer_id')->references('id')->on('transfers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asaas_transfers', function (Blueprint $table) {
            $table->dropForeign('asaas_transfers_transfer_id_foreign');
            $table->dropColumn('transfer_id');
        });
    }
}
