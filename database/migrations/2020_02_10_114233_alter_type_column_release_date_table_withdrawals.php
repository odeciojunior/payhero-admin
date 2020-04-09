<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTypeColumnReleaseDateTableWithdrawals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->timestamp('release_date_new')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn('release_date_new');
        });
    }
}
