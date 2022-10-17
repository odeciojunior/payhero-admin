<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContestationChargebacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("contestation_chargebacks", function (Blueprint $table) {
            $table->id();
            $table->bigInteger("sale_id")->unsigned();
            $table->string("case_number", 100);
            $table->tinyInteger("status_enum");
            $table->mediumText("data");
            $table->text("result");
            $table->timestamps();
        });

        Schema::table("contestation_chargebacks", function (Blueprint $table) {
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
        Schema::dropIfExists("contestation_chargebacks");
    }
}
