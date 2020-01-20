<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterWhiteBlackListTableAddCountColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('white_black_list', function(Blueprint $table) {
            $table->unsignedInteger("count")->after("description")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('white_black_list', function(Blueprint $table) {
            $table->dropColumn(["count"]);
        });
    }
}
