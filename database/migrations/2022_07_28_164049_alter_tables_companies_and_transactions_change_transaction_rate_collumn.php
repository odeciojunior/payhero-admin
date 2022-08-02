<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablesCompaniesAndTransactionsChangeTransactionRateCollumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('transaction_rate', 'transaction_tax');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->renameColumn('transaction_rate', 'transaction_tax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('transaction_tax', 'transaction_rate');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->renameColumn('transaction_tax', 'transaction_rate');
        });
    }
}
