<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFileUserCompletedToSaleContestationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sale_contestations", function (Blueprint $table) {
            $table->boolean("file_user_completed")->default(false);
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
            $table->dropColumn("file_user_completed");
        });
    }
}
