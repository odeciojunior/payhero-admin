<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddColunmsTaxesCardsSalesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->string('gateway_card_flag')->nullable();
            $table->decimal('gateway_tax_percent', 8, 2)->nullable();
            $table->integer('gateway_tax_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->dropColumn('gateway_card_flag');
            $table->dropColumn('gateway_tax_percent');
            $table->dropColumn('gateway_tax_value');
        });
    }
}
