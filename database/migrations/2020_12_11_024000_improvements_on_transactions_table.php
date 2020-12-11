<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImprovementsOnTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Drop old
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('antecipation_date');
            $table->dropColumn('antecipable_value');
            $table->dropColumn('antecipable_tax');
            $table->dropColumn('currency');
            $table->dropColumn('percentage_antecipable');
        });

        //Add new
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('gateway_id')->nullable()->after('sale_id');
            $table->boolean('is_waiting_withdrawal')->default(false);
            $table->integer('withdrawal_id')->nullable();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('gateway_id');
            $table->index('withdrawal_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Recreate old
        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('antecipation_date')->nullable()->after('installment_tax');
            $table->integer('antecipable_value')->nullable()->after('antecipation_date');
            $table->integer('antecipable_tax')->nullable()->after('antecipable_value');
            $table->string('currency')->nullable()->after('antecipable_tax');
            $table->string('percentage_antecipable')->nullable()->after('transaction_rate');
        });

        //Drop new
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['gateway_id']);
            $table->dropIndex(['withdrawal_id']);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('gateway_id');
            $table->dropColumn('is_waiting_withdrawal');
            $table->dropColumn('withdrawal_id');
        });

    }
}
