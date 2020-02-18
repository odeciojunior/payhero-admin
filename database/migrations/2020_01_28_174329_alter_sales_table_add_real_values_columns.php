<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSalesTableAddRealValuesColumns extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->unsignedInteger('real_total_paid_value')->nullable()->after('total_paid_value');
            $table->unsignedInteger('real_installments_amount')->nullable()->after('installments_amount');
            $table->unsignedInteger('real_installments_value')->nullable()->after('installments_value');
            $table->unsignedInteger('recovery_discount_percent')->nullable()->after('real_total_paid_value');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->dropColumn(["real_total_paid_value", "real_installments_amount", "real_installments_value", "recovery_discount_percent"]);
        });
    }
}
