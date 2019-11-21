<?php

use Illuminate\Support\Facades\DB;
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
        //Parte da tabela gateway_flag_taxes ------------------------------
        Schema::table('gateway_flag_taxes', function(Blueprint $table) {
            $table->dropForeign(['card_flag_id']);
            $table->renameColumn('card_flag_id', 'gateway_flag_id');
        });
        //------------------------------------------------------------------
        //Parte da tabela card_flags to  gateway_flags------------------------------
        Schema::table('card_flags', function(Blueprint $table) {
            $table->dropForeign(['gateway_id']);
        });
        Schema::table('card_flags', function(Blueprint $table) {
            $table->rename('gateway_flags');
        });
        Schema::table('gateway_flags', function(Blueprint $table) {
            $table->foreign('gateway_id')->references('id')->on('gateways');
        });
        $sql = "INSERT INTO gateway_flags (name,gateway_id, slug,card_flag_enum, created_at, updated_at) ";
        $sql .= "VALUES('Boleto',3,'boleto',10,CURRENT_DATE, CURRENT_DATE)";
        DB::select($sql);
        //---------------------------------------------------------------------
        //Parte da tabela gateway_flag_taxes ------------------------------
        Schema::table('gateway_flag_taxes', function(Blueprint $table) {
            $table->foreign('gateway_flag_id')->references('id')->on('gateway_flags');
        });
        //---------------------------------------------------------------------
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        //Parte da tabela gateway_flag_taxes ------------------------------
        Schema::table('gateway_flag_taxes', function(Blueprint $table) {
            $table->dropForeign(['gateway_flag_id']);
            $table->renameColumn('gateway_flag_id', 'card_flag_id');
        });
        //------------------------------------------------------------------
        //Parte da tabela gateway_flags to  card_flags------------------------------
        Schema::table('gateway_flags', function(Blueprint $table) {
            $table->dropForeign(['gateway_id']);
        });
        Schema::table('gateway_flags', function(Blueprint $table) {
            $table->rename('card_flags');
        });
        Schema::table('card_flags', function(Blueprint $table) {
            $table->foreign('gateway_id')->references('id')->on('gateways');
        });
        $sql = "DELETE FROM card_flags where slug='boleto'";
        DB::select($sql);
        //------------------------------------------------------------------
        //Parte da tabela gateway_flag_taxes ------------------------------
        Schema::table('gateway_flag_taxes', function(Blueprint $table) {
            $table->foreign('card_flag_id')->references('id')->on('card_flags');
        });
        //------------------------------------------------------------------
    }
}
