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
        Schema::create("company_balances", function (Blueprint $table) {
            $table->integerIncrements("id");
            $table->unsignedInteger("company_id")->index();
            $table->integer("vega_available_balance")->default(0);
            $table->integer("vega_pending_balance")->default(0);
            $table->integer("asaas_available_balance")->default(0);
            $table->integer("asaas_pending_balance")->default(0);
            $table->integer("cielo_available_balance")->default(0);
            $table->integer("cielo_pending_balance")->default(0);
            $table->integer("getnet_available_balance")->default(0);
            $table->integer("getnet_pending_balance")->default(0);
            $table->integer("gerencianet_available_balance")->default(0);
            $table->integer("gerencianet_pending_balance")->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table("company_balances", function (Blueprint $table) {
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("company_balances");
    }
};
