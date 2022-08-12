<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSalesAddAntifraudWarningLevelColumn extends Migration
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
                ->string("antifraud_warning_level", "20")
                ->after("observation")
                ->nullable()
                ->index();
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
            $table->dropColumn("antifraud_warning_level");
        });
    }
}
