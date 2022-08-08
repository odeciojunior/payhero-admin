<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleUnderAttacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("sale_under_attacks", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("under_attack_id");
            $table->unsignedBigInteger("sale_id");
            $table->timestamps();
        });

        Schema::table("sale_under_attacks", function (Blueprint $table) {
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
            $table
                ->foreign("under_attack_id")
                ->references("id")
                ->on("under_attacks");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("sale_under_attacks");
    }
}
