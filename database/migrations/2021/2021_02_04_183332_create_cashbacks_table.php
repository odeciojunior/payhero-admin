<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("cashbacks", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("company_id");
            $table->unsignedBigInteger("transaction_id")->nullable();
            $table->unsignedBigInteger("sale_id")->nullable();
            $table->integer("value");
            $table->integer("type_enum")->default(1);
            $table->integer("status")->default(1);
            $table->float("percentage")->nullable();
            $table->timestamps();
        });

        Schema::table("cashbacks", function (Blueprint $table) {
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
            $table
                ->foreign("transaction_id")
                ->references("id")
                ->on("transactions");
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("cashbacks");
    }
}
