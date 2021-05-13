<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnReleaseDateAndRenewNameColumnRealeseDateNewToReleaseDateTableWithdrawals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn('release_date');
            $table->renameColumn('release_date_new','release_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('release_date_table_withdrawals', function (Blueprint $table) {
            $table->renameColumn('release_date', 'release_date_new');
            $table->string('release_date');

        });
    }
}
