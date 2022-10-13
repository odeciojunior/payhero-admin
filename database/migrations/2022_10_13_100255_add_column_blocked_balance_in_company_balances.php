<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("company_balances", function (Blueprint $table) {
            $table
                ->integer("vega_blocked_balance")
                ->default(0)
                ->after("vega_pending_balance");
            $table
                ->integer("vega_total_balance")
                ->default(0)
                ->after("vega_blocked_balance");
            $table
                ->integer("asaas_blocked_balance")
                ->default(0)
                ->after("asaas_pending_balance");
            $table
                ->integer("asaas_total_balance")
                ->default(0)
                ->after("asaas_blocked_balance");
            $table
                ->integer("cielo_blocked_balance")
                ->default(0)
                ->after("cielo_pending_balance");
            $table
                ->integer("cielo_total_balance")
                ->default(0)
                ->after("cielo_blocked_balance");
            $table
                ->integer("getnet_blocked_balance")
                ->default(0)
                ->after("getnet_pending_balance");
            $table
                ->integer("getnet_total_balance")
                ->default(0)
                ->after("getnet_blocked_balance");
            $table
                ->integer("gerencianet_blocked_balance")
                ->default(0)
                ->after("gerencianet_pending_balance");
            $table
                ->integer("gerencianet_total_balance")
                ->default(0)
                ->after("gerencianet_blocked_balance");
            $table
                ->integer("total_balance")
                ->default(0)
                ->after("gerencianet_total_balance");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("company_balances", function (Blueprint $table) {
            //
        });
    }
};
