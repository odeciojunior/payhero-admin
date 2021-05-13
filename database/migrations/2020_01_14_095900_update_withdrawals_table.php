<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawals', function(Blueprint $table) {
            $table->string('currency')->after('value')->default('real');
            $table->string('currency_quotation')->nullable();
            $table->integer('value_transferred')->nullable();
            $table->integer('abroad_transfer_tax')->nullable();
            $table->integer('tax')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawals', function(Blueprint $table) {
            $table->dropColumn('currency');
            $table->dropColumn('currency_quotation');
            $table->dropColumn('value_transferred');
            $table->dropColumn('abroad_transfer_tax');
            $table->dropColumn('tax');
        });
    }
}
