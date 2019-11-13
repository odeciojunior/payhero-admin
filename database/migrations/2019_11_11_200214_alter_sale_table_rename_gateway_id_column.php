<?php

use Illuminate\Support\Facades\DB;
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
                });

                Schema::table('sales', function(Blueprint $table) {
                    $table->unsignedBigInteger('gateway_id')->nullable()->index();
                });

                Schema::table('sales', function(Blueprint $table) {
                    $table->foreign('gateway_id')->references('id')->on('gateways');
                });


                $sql = "UPDATE sales SET gateway_id = 1 "; // 1 - pagarme_production
                DB::select($sql);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        $sql = "UPDATE sales SET gateway_id = null "; // 1 - pagarme_production
        DB::select($sql);

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
