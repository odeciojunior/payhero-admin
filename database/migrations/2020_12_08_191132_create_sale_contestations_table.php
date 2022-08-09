<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleContestationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("sale_contestations", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("sale_id")->nullable();
            $table->json("data")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table("sale_contestations", function (Blueprint $table) {
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sale_contestations", function (Blueprint $table) {
            $table->dropForeign(["sale_id"]);
        });

        Schema::dropIfExists("sale_contestations");
    }
}
