<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckoutTaxColumnToCompaniesAndTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('checkout_tax')->default(0)->after('installment_tax');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('checkout_tax')->default(0)->after('installment_tax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('checkout_tax');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('checkout_tax');
        });
    }
}
