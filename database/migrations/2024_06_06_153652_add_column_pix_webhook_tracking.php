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
            $table->integer('pix_flag')->nullable()-> after('credit_flag')->default(0);
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
            $table->dropColumn('pix_flag');
        });
    }
};
