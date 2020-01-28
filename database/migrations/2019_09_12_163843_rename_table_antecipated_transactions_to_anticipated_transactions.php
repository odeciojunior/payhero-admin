<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTableAntecipatedTransactionsToAnticipatedTransactions extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::rename('antecipated_transactions', 'anticipated_transactions');
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::rename('anticipated_transactions', 'antecipated_transactions');
    }
}
