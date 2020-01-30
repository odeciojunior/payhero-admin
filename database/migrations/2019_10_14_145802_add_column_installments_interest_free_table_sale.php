<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInstallmentsInterestFreeTableSale extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->string('installment_tax')->nullable()->after('installments_value');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->dropColumn('installment_tax');
        });
    }
}

