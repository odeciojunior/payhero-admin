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
        Schema::table('user_tax_logs', function (Blueprint $table) {
            $table->string("reason",120)->nullable()->default(null)->after("properties");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_tax_logs', function (Blueprint $table) {
            $table->removeColumn("reason");
        });
    }
};
