<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyBalanceLogsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create("company_balance_logs", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("company_id");
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
            $table->bigInteger("today_balance")->nullable();
            $table->bigInteger("pending_balance")->nullable();
            $table->bigInteger("available_balance")->nullable();
            $table->bigInteger("total_balance")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("company_balance_logs");
    }
}
