<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTransactionsTableAddInformations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function(Blueprint $table) {
            $table->string('currency')->nullable();
            $table->string('percentage_rate')->nullable();
            $table->string('transaction_rate')->nullable();
            $table->string('percentage_antecipable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function(Blueprint $table) {
            $table->dropColumn('currency');
            $table->dropColumn('percentage_rate');
            $table->dropColumn('transaction_rate');
            $table->dropColumn('percentage_antecipable');
        });
    }


}
