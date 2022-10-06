<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargebacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("chargebacks", function (Blueprint $table) {
            $table->id();
            $table->bigInteger("sale_id")->unsigned();
            $table->string("case_number", 100);
            $table->tinyInteger("status_enum");
            $table->timestamps();
        });

        Schema::table("chargebacks", function (Blueprint $table) {
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
        });

        Schema::table("chargebacks", function (Blueprint $table) {
            $table->index("case_number");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("chargebacks");
    }
}
