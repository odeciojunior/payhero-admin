<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAntifraudWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("antifraud_warnings", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("sale_id")->nullable();
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
            $table->tinyInteger("status")->index();
            $table->string("column")->index();
            $table->string("value");
            $table->string("level", 20);
            $table->timestamps();
            $table->unique(["sale_id", "column", "value"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("antifraud_warnings", function (Blueprint $table) {
            Schema::dropIfExists("antifraud_warnings");
        });
    }
}
