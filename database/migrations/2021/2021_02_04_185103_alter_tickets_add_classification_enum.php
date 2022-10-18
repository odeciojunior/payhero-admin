<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTicketsAddClassificationEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("tickets", function (Blueprint $table) {
            $table
                ->tinyInteger("classification_enum")
                ->after("ignore_balance_block")
                ->nullable();
            $table
                ->integer("average_response_time")
                ->after("ignore_balance_block")
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("tickets", function (Blueprint $table) {
            $table->dropColumn("classification_enum");
            $table->dropColumn("average_response_time");
        });
    }
}
