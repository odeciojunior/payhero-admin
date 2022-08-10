<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSaleAddColumnAnticipationIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sales", function (Blueprint $table) {
            $table
                ->string("anticipation_id")
                ->nullable()
                ->after("anticipation_status");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sales", function (Blueprint $table) {
            $table->dropColumn("anticipation_id");
        });
    }
}
