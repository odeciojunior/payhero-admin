<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSalesRenameColumnClientId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->renameColumn('client_id', 'customer_id');
            $table->renameColumn('client_card_id', 'customer_card_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->renameColumn('customer_id', 'client_id');
            $table->renameColumn('customer_card_id', 'client_card_id');
        });
    }
}
