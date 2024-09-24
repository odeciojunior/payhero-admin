<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sale_informations", function (Blueprint $table) {
            $table
                ->text("return_url")
                ->nullable()
                ->after("referer");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sale_informations", function (Blueprint $table) {
            $table->dropColumn("return_url");
        });
    }
};
