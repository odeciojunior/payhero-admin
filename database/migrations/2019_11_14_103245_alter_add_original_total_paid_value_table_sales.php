<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddOriginalTotalPaidValueTableSales extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->integer('original_total_paid_value')->nullable()->after('total_paid_value');
        });
        \Illuminate\Support\Facades\DB::statement('UPDATE sales 
        set original_total_paid_value = (total_paid_value*100);');
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->dropColumn('original_total_paid_value');
        });
    }
}
