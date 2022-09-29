<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_biometry_results', function (Blueprint $table) {
            $table->dropColumn("api_data");
            $table->dropColumn("postback_data");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_biometry_results', function (Blueprint $table) {
            $table->json("postback_data")->nullable();
            $table->json("api_data")->nullable();
        });
    }
};
