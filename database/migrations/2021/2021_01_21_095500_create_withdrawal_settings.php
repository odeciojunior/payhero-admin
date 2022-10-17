<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("withdrawal_settings", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->index();
            $table->string("rule")->nullable();
            $table->string("frequency")->nullable();
            $table
                ->unsignedTinyInteger("weekday")
                ->nullable()
                ->default(0);
            $table
                ->unsignedTinyInteger("day")
                ->nullable()
                ->default(1);
            $table->integer("amount")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table("withdrawal_settings", function (Blueprint $table) {
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
        Schema::dropIfExists("withdrawal_settings");
    }
}
