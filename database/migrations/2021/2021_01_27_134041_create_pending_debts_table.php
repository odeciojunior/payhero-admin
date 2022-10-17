<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("pending_debts", function (Blueprint $table) {
            $table->id();
            $table->integer("company_id")->unsigned();
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
            $table->unsignedBigInteger("sale_id")->nullable();
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");

            $table->enum("type", ["REVERSED", "ADJUSTMENT"]);

            $table->dateTime("request_date")->nullable();
            $table->date("confirm_date")->nullable();
            $table->date("payment_date")->nullable();
            $table->string("reason")->nullable();
            $table->unsignedInteger("value");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("pending_debts");
    }
}
