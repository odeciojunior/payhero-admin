<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTicketsDropClassificationEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("tickets", function (Blueprint $table) {
            $table->dropColumn("classification_enum");
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
            $table
                ->tinyInteger("classification_enum")
                ->after("ignore_balance_block")
                ->nullable();
        });
    }
}
