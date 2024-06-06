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
        Schema::table('webhook_trackings', function (Blueprint $table) {
            $table->integer('credit_flag')->nullable()-> after('webhook_url')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webhook_trackings', function (Blueprint $table) {
            $table->dropColumn('credit_flag');
        });
    }
};
