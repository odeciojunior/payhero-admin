<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClosingDateToPendingDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_debts', function (Blueprint $table) {
            $table->date('closing_date')
                ->nullable()->default(null)->after('request_date');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('pending_debts', function (Blueprint $table) {
            $table->dropColumn('closing_date');
        });

    }
}
