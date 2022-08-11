<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockReasonSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("block_reason_sales", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("sale_id");
            $table->unsignedBigInteger("blocked_reason_id");
            $table->tinyInteger("status")->default(1);
            $table->string("observation");
            $table->timestamps();
        });

        Schema::table("block_reason_sales", function (Blueprint $table) {
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
            $table
                ->foreign("blocked_reason_id")
                ->references("id")
                ->on("block_reasons");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("block_reason_sales");
    }
}
